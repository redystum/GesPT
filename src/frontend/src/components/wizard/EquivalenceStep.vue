<template>
  <div>
    <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">{{ $t('wizard.equivalence_step.title') }}</h2>

    <div class="mb-4">
      <label class="block text-sm font-medium mb-1">{{ $t('wizard.equivalence_step.previous_plan') }} *</label>
      <select
        v-if="!loadingPlan"
        v-model="selectedPrevPlanId"
        class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
      >
        <option value="" disabled>{{ $t('wizard.equivalence_step.select_previous_plan') }}</option>
        <option v-for="plan in filteredPrevPlans" :key="plan.id" :value="plan.id">
          {{ plan.ano }} ({{ plan.curso?.nome || $t('common.na') }})
        </option>
      </select>
      <div v-else class="text-sm p-3 text-neutral-500">
        {{ $t('wizard.equivalence_step.loading_latest_plan') }}
      </div>
      <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
        {{ $t('wizard.equivalence_step.latest_auto_selected') }}
      </p>
    </div>

    <div
      v-if="selectedPrevPlanId"
      class="border-t pt-4 border-neutral-200 dark:border-neutral-700"
    >
      <h3 class="font-semibold text-lg mb-2">{{ $t('wizard.equivalence_step.map_title') }}</h3>
      <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">
        {{ $t('wizard.equivalence_step.map_description') }}
      </p>

      <div
        v-if="equivalences.length === 0"
        class="rounded-md border border-dashed border-neutral-300 dark:border-neutral-700 p-4 text-sm text-neutral-500 dark:text-neutral-400"
      >
        {{ $t('wizard.equivalence_step.empty_prefix') }} <strong>{{ $t('wizard.equivalence_step.add_button_full') }}</strong> {{ $t('wizard.equivalence_step.empty_suffix') }}
      </div>

      <div
        v-for="(equiv, index) in equivalences"
        :key="index"
        class="mb-4 p-4 border rounded-md dark:border-neutral-700"
      >
        <div class="flex items-start justify-between gap-3 mb-4">
          <div class="flex-1">
            <label class="block text-xs font-semibold mb-1">{{ $t('wizard.equivalence_step.new_uc') }}</label>
            <div class="relative">
              <SearchableDropdown
                v-model="equiv.newUcIndex"
                :options="newPlanUcs.map((u, idx) => ({ ...u, origIndex: idx }))"
                value-key="origIndex"
                :display-fn="u => `${u.nome} (${$t('wizard.equivalence_step.sem_abbr')} ${u.semestre || '-'})`"
                :search-fn="(u, q) => normalizeString(u.nome?.toLowerCase() || '').includes(q)"
                @change="onNewUcChange(index)"
                :placeholder="$t('wizard.equivalence_step.search_new_uc_placeholder')"
                input-class="text-sm"
              />
            </div>
          </div>
          <div class="pt-6">
            <button
              @click="removeEquiv(index)"
              class="text-red-500 font-bold ml-2 cursor-pointer"
              :title="$t('wizard.equivalence_step.remove_equivalence')"
            >
              <i class="fa fa-remove" />
            </button>
          </div>
        </div>

        <div
          v-if="equiv.newUcIndex !== ''"
          class="bg-neutral-50 dark:bg-neutral-900/30 rounded-md border dark:border-neutral-700 p-3"
        >
          <div class="text-xs font-semibold mb-2 text-neutral-700 dark:text-neutral-300">
            {{ $t('wizard.equivalence_step.old_uc_same_semester') }} ({{ getEquivSemester(equiv) || '-' }}) *
          </div>

          <div v-if="equiv.loading" class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $t('wizard.equivalence_step.loading_old_ucs') }}
          </div>

          <div
            v-else-if="getFilteredOldUcs(equiv).length === 0"
            class="text-sm text-neutral-500 dark:text-neutral-400 italic"
          >
            {{ $t('wizard.equivalence_step.no_old_ucs_found') }}
          </div>

          <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
              <label
                v-for="oldUc in getFilteredOldUcs(equiv)"
                :key="oldUc.id"
                class="flex items-center gap-2 text-sm border rounded px-2 py-1 cursor-pointer dark:border-neutral-700"
              >
                <input
                  type="checkbox"
                  :value="oldUc.id"
                  v-model="equiv.oldUcs"
                  class="cursor-pointer"
                />
                <span>{{ oldUc.nome || oldUc.uc?.nome }} ({{ oldUc.sigla || oldUc.uc?.sigla }})</span>
              </label>
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-xs font-semibold mb-1">{{ $t('wizard.equivalence_step.observations') }}</label>
            <textarea
              v-model.lazy="equiv.observacoes"
              class="w-full rounded-md border p-2 text-sm bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
              rows="2"
              :placeholder="$t('wizard.equivalence_step.observations_placeholder')"
            ></textarea>
          </div>
        </div>
      </div>

      <button
        @click="addEquiv"
        class="mt-2 text-blue-500 hover:text-blue-700 text-sm font-medium cursor-pointer"
      >
        <i class="fa fa-plus text-xs"/> {{ $t('wizard.equivalence_step.add_button') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { usePlanWizardStore } from '../../stores/planWizard'
import SearchableDropdown from '../SearchableDropdown.vue'

const props = defineProps({
  modelValue: { type: Object, default: () => ({ prevPlanId: '', equivalences: [] }) },
  newPlanUcs: { type: Array, default: () => [] },
  courseData: { type: Object, default: () => ({}) },
  isEditing: { type: Boolean, default: false }
})
const emit = defineEmits(['update:modelValue'])

const store = usePlanWizardStore()
const { wizardData, prevPlanData, prevPlans, prevUcs, prevUcsBySemester, loadingPlan } = storeToRefs(store)

const selectedPrevPlanId = computed({
  get: () => store.wizardData.equivalences.prevPlanId,
  set: (val) => { store.wizardData.equivalences.prevPlanId = val }
})

const filteredPrevPlans = computed(() => {
  const currentPlanId = wizardData.value.plan?.id
  if (!currentPlanId) return prevPlans.value
  return prevPlans.value.filter((plan) => plan.id !== currentPlanId)
})
const equivalences = ref([])

const newPlanUcs = computed(() => props.newPlanUcs || [])

const normalizeString = (str) => {
  return str ? str.normalize('NFD').replace(/[\u0300-\u036f]/g, "") : ''
}

const getEquivSemester = (equiv) => {
  const idx = Number(equiv.newUcIndex)
  if (Number.isNaN(idx) || !newPlanUcs.value[idx]) return null
  return newPlanUcs.value[idx]?.semestre ?? null
}

onMounted(() => {
  const courseId = props.courseData?.data?.id
  store.fetchPrevPlans(courseId)
  if (!props.isEditing) {
    store.fetchLatestPlan(courseId)
  }

  if (props.modelValue) {
    store.wizardData.equivalences.prevPlanId = props.modelValue.prevPlanId || ''
    equivalences.value = (props.modelValue.equivalences || []).map((eq) => ({
      newUcIndex: eq.newUcIndex ?? '',
      oldUcs: Array.isArray(eq.oldUcs) ? [...eq.oldUcs] : [],
      observacoes: eq.observacoes || '',
      loading: false
    }))
  }
})

watch(selectedPrevPlanId, (newId) => {
  if (newId) {
    store.fetchPrevPlanUcs(newId)
    prevUcsBySemester.value = {}
  } else {
    prevUcs.value = []
    prevUcsBySemester.value = {}
  }
})

watch([selectedPrevPlanId, equivalences], () => {
  emit('update:modelValue', {
    prevPlanId: selectedPrevPlanId.value,
    equivalences: equivalences.value.map((eq) => {
      const idx = Number(eq.newUcIndex)
      const selectedNewUc = Number.isNaN(idx) ? null : newPlanUcs.value[idx]
      return {
        newUcIndex: eq.newUcIndex,
        newUc: selectedNewUc?.id || selectedNewUc?.sigla || selectedNewUc?.nome || '',
        oldUcs: eq.oldUcs,
        observacoes: eq.observacoes
      }
    })
  })
}, { deep: true })

watch(
  () => props.courseData?.data?.id,
  (courseId) => {
    if (!courseId) {
      prevPlanData.value = null
      store.wizardData.equivalences.prevPlanId = ''
      prevUcs.value = []
      prevUcsBySemester.value = {}
      return
    }

    store.fetchPrevPlans(courseId)
    store.fetchLatestPlan(courseId)
  }
)

const getFilteredOldUcs = (equiv) => {
  const semester = getEquivSemester(equiv)
  if (!semester) return prevUcs.value
  return prevUcsBySemester.value[semester] || prevUcs.value.filter((uc) => Number(uc.semestre) === Number(semester))
}

const onNewUcChange = async (index) => {
  const equiv = equivalences.value[index]
  if (!equiv) return

  const semester = getEquivSemester(equiv)
  equiv.oldUcs = []

  if (!selectedPrevPlanId.value || !semester) return
  if (prevUcsBySemester.value[semester]) return

  const originalLoadingState = true
  equiv.loading = originalLoadingState
  const fetched = await store.fetchPrevPlanUcs(selectedPrevPlanId.value, semester)
  if (!fetched?.length && prevUcs.value.length) {
    prevUcsBySemester.value = {
      ...prevUcsBySemester.value,
      [semester]: prevUcs.value.filter((uc) => Number(uc.semestre) === Number(semester))
    }
  }
  equiv.loading = false
}

const addEquiv = () => {
  equivalences.value.push({
    newUcIndex: '',
    oldUcs: [],
    observacoes: '',
    loading: false
  })
}

const removeEquiv = (index) => {
  equivalences.value.splice(index, 1)
}
</script>