<template>
  <div class="max-w-7xl mx-auto px-6 py-10 animate-fade-in-up" @keydown.enter="handleEnterAdvance">
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
              currentStep === index + 1 && hasStepErrors(index + 1)
                ? 'bg-red-600 dark:bg-red-500 text-white'
                : currentStep === index + 1
                  ? 'bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900'
                : hasStepErrors(index + 1)
                  ? 'bg-red-600 dark:bg-red-500 text-white'
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
              currentStep === index + 1 && hasStepErrors(index + 1)
                ? 'text-red-700 dark:text-red-400'
                : currentStep === index + 1
                  ? 'text-neutral-900 dark:text-neutral-100'
                : hasStepErrors(index + 1)
                  ? 'text-red-700 dark:text-red-400'
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
          :class="
            isStepCompleted(index + 1)
              ? 'bg-green-500 dark:bg-green-400'
              : 'bg-neutral-300 dark:bg-neutral-700'
          "
        ></div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div
      class="grid grid-cols-1 lg:grid-cols-3 gap-8"
      :class="currentStep === 4 ? 'lg:grid-cols-4!' : ''"
    >
      <div
        class="lg:col-span-2 rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] p-6 sm:p-8"
      >
        <!-- Dynamic Component Step -->
        <template v-if="isStepUnlocked(currentStep)">
          <div
            v-if="submitErrorMessage"
            class="mb-5 rounded-md border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-4"
          >
            <p class="text-sm font-semibold text-red-800 dark:text-red-300">{{ submitErrorMessage }}</p>
            <ul
              v-if="currentStepErrors.length"
              class="mt-2 list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1"
            >
              <li v-for="(errorMessage, idx) in currentStepErrors" :key="`${idx}-${errorMessage}`">
                {{ errorMessage }}
              </li>
            </ul>
          </div>

          <div
            v-if="submitSuccessMessage"
            class="mb-5 rounded-md border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 p-4 text-sm font-semibold text-green-800 dark:text-green-300"
          >
            {{ submitSuccessMessage }}
          </div>

          <component
            :is="steps[currentStep - 1].component"
            v-model="wizardData[steps[currentStep - 1].key]"
            :newPlanUcs="wizardData.ucs"
            :courseData="wizardData.course"
          />
        </template>
        <template v-else>
          <div
            class="rounded-md border border-amber-200 dark:border-amber-700 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-300"
          >
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
      <div class="space-y-6" :class="currentStep === 4 ? 'lg:col-span-2' : ''">
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
        <div
          class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6"
          v-if="currentStep === 4"
        >
          <CompareTable :equivalences="planPreview" :plano_final="true" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, inject, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { usePlanWizardStore } from '../stores/planWizard'
import CourseStep from '../components/wizard/CourseStep.vue'
import PlanStep from '../components/wizard/PlanStep.vue'
import UcStep from '../components/wizard/UcStep.vue'
import EquivalenceStep from '../components/wizard/EquivalenceStep.vue'
import CompareTable from '@/components/CompareTable.vue'

const store = usePlanWizardStore()
const api = inject('api')
const { wizardData, planPreview } = storeToRefs(store)

const currentStep = ref(1)
const submitErrorMessage = ref('')
const submitSuccessMessage = ref('')
const validationErrors = ref({})

const isStepUnlocked = (stepNumber) => Boolean(steps.value[stepNumber - 1]?.rule)

const stepErrorMap = {
  1: ['course'],
  2: ['plan'],
  3: ['ucs'],
  4: ['equivalences']
}

const flattenErrorMessages = (errorsObject) => {
  if (!errorsObject || typeof errorsObject !== 'object') return []
  return Object.values(errorsObject).flatMap((messages) =>
    Array.isArray(messages) ? messages : [String(messages)]
  )
}

const hasStepErrors = (stepNumber) => {
  const keys = Object.keys(validationErrors.value || {})
  const stepPrefixes = stepErrorMap[stepNumber] || []
  return keys.some((errorKey) => stepPrefixes.some((prefix) => errorKey.startsWith(prefix)))
}

const currentStepErrors = computed(() => {
  const keys = Object.keys(validationErrors.value || {})
  const stepPrefixes = stepErrorMap[currentStep.value] || []
  const filteredErrors = keys.reduce((acc, key) => {
    if (stepPrefixes.some((prefix) => key.startsWith(prefix))) {
      acc[key] = validationErrors.value[key]
    }
    return acc
  }, {})
  return flattenErrorMessages(filteredErrors)
})

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

const getCourseSelectionKey = (course) => {
  if (!course) return 'none'
  if (course.isNew) return 'new'

  const selectedId = course.data?.id
  return selectedId ? `existing:${selectedId}` : 'none'
}

watch(
  () => getCourseSelectionKey(wizardData.value.course),
  (newKey, oldKey) => {
    if (!oldKey) return
    if (newKey === oldKey) return

    wizardData.value.ucs = []
    wizardData.value.equivalences = {
      prevPlanId: '',
      equivalences: []
    }
  }
)

const nextStep = () => {
  submitErrorMessage.value = ''
  submitSuccessMessage.value = ''
  if (!isNextDisabled.value && currentStep.value < steps.value.length) currentStep.value++
}

const handleEnterAdvance = (event) => {
  if (event.isComposing || event.shiftKey || event.ctrlKey || event.altKey || event.metaKey) return
  if (currentStep.value >= steps.value.length || isNextDisabled.value) return

  const target = event.target
  const tagName = target?.tagName?.toLowerCase()
  const isEditable = target?.isContentEditable

  if (tagName === 'textarea' || tagName === 'select' || isEditable) return

  event.preventDefault()
  nextStep()
}

const prevStep = () => {
  submitErrorMessage.value = ''
  submitSuccessMessage.value = ''
  if (currentStep.value > 1) currentStep.value--
}

const goToStep = (stepNumber) => {
  submitErrorMessage.value = ''
  submitSuccessMessage.value = ''
  if (canNavigateToStep(stepNumber)) {
    currentStep.value = stepNumber
  }
}

watch(steps, () => {
  if (!isStepUnlocked(currentStep.value)) {
    currentStep.value = Math.max(1, currentStep.value - 1)
  }
})

const finishWizard = async () => {
  submitErrorMessage.value = ''
  submitSuccessMessage.value = ''
  validationErrors.value = {}

  try {
    await api.post('/wizard', wizardData.value)
    submitSuccessMessage.value = 'Plano guardado com sucesso.'
  } catch (error) {
    const responseData = error?.response?.data
    const backendErrors = responseData?.errors
    validationErrors.value = backendErrors && typeof backendErrors === 'object' ? backendErrors : {}
    submitErrorMessage.value = responseData?.message || 'Ocorreu um erro ao guardar o plano.'

    // Keep users focused by moving to the first step that contains server-side validation errors.
    const firstErroredStep = [1, 2, 3, 4].find((stepNumber) => hasStepErrors(stepNumber))
    if (firstErroredStep) {
      currentStep.value = firstErroredStep
    }
  }
}
</script>
<style scoped></style>
