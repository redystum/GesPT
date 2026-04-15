<template>
  <div class="max-w-7xl mx-auto px-6 py-10 animate-fade-in-up">
    <!-- Header -->
    <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100 mb-8 tracking-tight">
      {{ $t('wizard.title') || 'Create Plan Wizard' }}
    </h1>

    <!-- Progress Stepper -->
    <div class="flex items-center gap-4 mb-10 text-sm font-medium">
      <div v-for="(step, index) in steps" :key="index" class="flex items-center gap-2">
        <button
          @click="goToStep(index + 1)"
          :disabled="!canNavigateToStep(index + 1)"
          :aria-disabled="!canNavigateToStep(index + 1)"
          :aria-current="currentStep === index + 1 ? 'step' : undefined"
          class="flex items-center gap-2 transition-opacity focus:outline-none disabled:cursor-not-allowed enabled:cursor-pointer"
          :class="[canNavigateToStep(index + 1) ? 'hover:opacity-80' : 'opacity-50']"
        >
          <span
            class="w-6 h-6 rounded flex items-center justify-center text-xs"
            :class="[
              currentStep === index + 1
                ? 'bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900'
                : isStepCompleted(index + 1)
                  ? 'bg-green-600 dark:bg-green-500 text-white'
                  : canNavigateToStep(index + 1)
                    ? 'border border-neutral-300 dark:border-neutral-700 text-neutral-500 dark:text-neutral-400'
                    : 'border border-neutral-200 dark:border-neutral-800 text-neutral-400 dark:text-neutral-600'
            ]"
          >
            {{ isStepCompleted(index + 1) ? '✓' : index + 1 }}
          </span>
          <span
            :class="[
              currentStep === index + 1
                ? 'text-neutral-900 dark:text-neutral-100'
                : canNavigateToStep(index + 1)
                  ? 'text-neutral-500 dark:text-neutral-400'
                  : 'text-neutral-400 dark:text-neutral-600'
            ]"
          >
            {{ step.label }}
          </span>
        </button>
        <div
          v-if="index < steps.length - 1"
          class="w-8 h-px ml-2"
          :class="isStepCompleted(index + 1) ? 'bg-green-500 dark:bg-green-400' : 'bg-neutral-300 dark:bg-neutral-700'"
        ></div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div
        class="lg:col-span-2 rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] p-6 sm:p-8"
      >
        <!-- Dynamic Component Step -->
        <template v-if="isStepUnlocked(currentStep)">
          <component
            :is="steps[currentStep - 1].component"
            v-model="wizardData[steps[currentStep - 1].key]"
            :newPlanUcs="wizardData.ucs"
            :courseData="wizardData.course"
          />
        </template>
        <template v-else>
          <div class="rounded-md border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-300">
            This step is locked. Complete the required previous step to unlock it.
          </div>
        </template>

        <!-- Controls -->
        <div
          class="mt-8 pt-6 border-t border-t-neutral-200 dark:border-t-neutral-800 flex justify-between"
        >
          <button
            @click="prevStep"
            :disabled="isPrevDisabled"
            class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 disabled:opacity-50 disabled:cursor-not-allowed enabled:cursor-pointer"
          >
            {{ $t('common.back') || 'Back' }}
          </button>

          <button
            v-if="currentStep < steps.length"
            @click="nextStep"
            :disabled="isNextDisabled"
            :title="nextDisabledReason"
            class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 border border-neutral-800 dark:border-neutral-300 disabled:opacity-50 disabled:cursor-not-allowed enabled:cursor-pointer"
          >
            {{ $t('common.next') || 'Next' }}
          </button>

          <button
            v-else
            @click="finishWizard"
            class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-green-600 dark:bg-green-500 text-white border border-green-700 dark:border-green-400 hover:bg-green-700 cursor-pointer"
          >
            Save Plan
          </button>
        </div>
      </div>

      <!-- Summary -->
      <div class="space-y-6">
        <div
          class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6"
        >
          <h3 class="font-bold text-sm mb-4 text-neutral-900 dark:text-neutral-100">Summary</h3>
          <div class="space-y-3 text-sm pb-4 border-b">
            <div class="flex flex-col gap-1 pb-2 text-neutral-600 dark:text-neutral-400">
              <span class="font-semibold text-neutral-800 dark:text-neutral-200">Course Data</span>
              <span>New? {{ wizardData.course.isNew ? 'Yes' : 'No' }}</span>
              <span
                >Selection:
                {{ wizardData.course.data?.nome || wizardData.course.data?.id || '?' }}</span
              >
            </div>
            <div
              class="flex justify-between items-center pb-2 text-neutral-600 dark:text-neutral-400"
            >
              <span class="font-semibold text-neutral-800 dark:text-neutral-200">Year</span>
              <span class="font-medium text-neutral-900 dark:text-neutral-100">{{
                wizardData.plan.ano || '-'
              }}</span>
            </div>
            <div class="flex flex-col gap-2 text-neutral-600 dark:text-neutral-400">
              <div class="flex justify-between items-center">
                <span class="font-semibold text-neutral-800 dark:text-neutral-200"
                  >UCs Configured</span
                >
                <span class="font-medium text-neutral-900 dark:text-neutral-100">{{
                  wizardData.ucs.length
                }}</span>
              </div>
              <ul
                v-if="wizardData.ucs.length > 0"
                class="list-disc list-inside pl-2 text-xs space-y-1"
              >
                <li
                  v-for="(uc, idx) in wizardData.ucs"
                  :key="idx"
                  class="text-neutral-700 dark:text-neutral-300"
                >
                  {{ uc.nome || uc.sigla || 'Unnamed UC' }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { usePlanWizardStore } from '../stores/planWizard'
import CourseStep from '../components/wizard/CourseStep.vue'
import PlanStep from '../components/wizard/PlanStep.vue'
import UcStep from '../components/wizard/UcStep.vue'
import EquivalenceStep from '../components/wizard/EquivalenceStep.vue'

const store = usePlanWizardStore()
const { wizardData } = storeToRefs(store)

const currentStep = ref(1)

const isStepUnlocked = (stepNumber) => Boolean(steps.value[stepNumber - 1]?.rule)

const canNavigateToStep = (stepNumber) => {
  if (stepNumber < 1 || stepNumber > steps.value.length) return false
  if (!isStepUnlocked(stepNumber)) return false

  // If current plan info is invalid, prevent jumps to future steps.
  if (currentStep.value === 2 && stepNumber > 2 && wizardData.value.plan.isValid === false) {
    return false
  }

  return true
}

const isStepCompleted = (stepNumber) => {
  if (stepNumber >= currentStep.value) return false
  return isStepUnlocked(stepNumber)
}

const isPrevDisabled = computed(() => currentStep.value === 1)
const isNextDisabled = computed(() => {
  if (currentStep.value >= steps.value.length) return true
  if (currentStep.value === 2 && wizardData.value.plan.isValid === false) return true
  return !isStepUnlocked(currentStep.value + 1)
})

const nextDisabledReason = computed(() => {
  if (currentStep.value === 2 && wizardData.value.plan.isValid === false) {
    return 'Fill a valid academic year to continue.'
  }

  if (!isStepUnlocked(currentStep.value + 1)) {
    return 'Complete the required data to unlock the next step.'
  }

  return ''
})


const steps = computed(() => [
  { label: 'Course', key: 'course', component: CourseStep, rule: true },
  { label: 'Plan Info', key: 'plan', component: PlanStep, rule: true },
  {
    label: 'Unidades Curriculares (UCs)',
    key: 'ucs',
    component: UcStep,
    rule: wizardData.value.course.isNew || wizardData.value.course.data.id > 0
  },
  {
    label: 'Equivalences',
    key: 'equivalences',
    component: EquivalenceStep,
    rule: wizardData.value.ucs.length > 0
  }
])

const nextStep = () => {
  if (!isNextDisabled.value && currentStep.value < steps.value.length) currentStep.value++
}

const prevStep = () => {
  if (currentStep.value > 1) currentStep.value--
}

const goToStep = (stepNumber) => {
  if (canNavigateToStep(stepNumber)) {
    currentStep.value = stepNumber
  }
}

watch(steps, () => {
  if (!isStepUnlocked(currentStep.value)) {
    currentStep.value = Math.max(1, currentStep.value - 1)
  }
})

const finishWizard = () => {
  console.log('Saving wizard data:', wizardData.value)
  alert('Plan Saved! Check console for payload.')
}
</script>
<style scoped></style>
