<template>
  <div>
    <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">
      {{ $t('wizard.uc_step.title') }}
    </h2>

    <div class="mb-4">
      <h3 class="font-semibold text-lg mb-2">{{ $t('wizard.uc_step.current_ucs') }}</h3>
      <div v-if="ucs.length === 0" class="text-neutral-500 italic">
        {{ $t('wizard.uc_step.empty') }}
      </div>
      <ul class="space-y-2">
        <li
          v-for="(uc, index) in ucs"
          :key="index"
          class="p-3 border rounded-md flex justify-between items-center bg-neutral-50 dark:bg-neutral-800 dark:border-neutral-700"
        >
          <div>
            <div class="font-medium">{{ uc.nome }} ({{ uc.sigla }})</div>
            <div class="text-xs text-neutral-600 dark:text-neutral-400">
              {{ $t('wizard.uc_step.ects') }}: {{ uc.ects }} | {{ $t('wizard.uc_step.hours') }}:
              {{ uc.horas_trabalho_totais ?? uc.horas }} | {{ $t('wizard.uc_step.semester') }}:
              {{ uc.semestre }} | {{ $t('wizard.uc_step.year') }}: {{ uc.ano }}
            </div>
          </div>
          <button
            @click="removeUc(index)"
            class="text-red-500 hover:text-red-700 text-sm cursor-pointer"
          >
            {{ $t('wizard.uc_step.remove') }}
          </button>
        </li>
      </ul>
    </div>

    <div class="border-t pt-4 border-neutral-200 dark:border-neutral-700">
      <h3 class="font-semibold text-lg mb-4">{{ $t('wizard.uc_step.add_new_title') }}</h3>

      <div
        class="mb-4 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-md border border-blue-100 dark:border-blue-900"
      >
        <label class="block text-sm font-medium mb-1 text-blue-800 dark:text-blue-300">
          {{ $t('wizard.uc_step.select_existing') }}
          <span v-if="loadingUcs" class="text-sm text-blue-500 font-normal ml-2">{{
            $t('common.loading')
          }}</span>
        </label>
        <div class="flex gap-2">
          <div class="relative flex-1">
            <SearchableDropdown
              v-model="selectedExistingUc"
              :options="dbUcs"
              :return-object="true"
              :display-fn="(u) => `${u.nome} (${u.sigla})`"
              :search-fn="
                (u, q) =>
                  normalizeString(u.nome?.toLowerCase() || '').includes(q) ||
                  normalizeString(u.sigla?.toLowerCase() || '').includes(q)
              "
              :placeholder="$t('wizard.uc_step.search_placeholder')"
            />
          </div>
          <button
            @click="addExistingUc"
            :disabled="!selectedExistingUc"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed enabled:cursor-pointer"
          >
            {{ $t('wizard.uc_step.add_selected') }}
          </button>
        </div>
      </div>

      <div class="relative py-2">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-neutral-200 dark:border-neutral-700"></div>
        </div>
        <div class="relative flex justify-center">
          <span class="bg-white dark:bg-[#1a1a1a] px-2 text-xs text-neutral-500 uppercase">{{
            $t('wizard.uc_step.or_create')
          }}</span>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 mt-4">
        <div>
          <label class="block text-sm font-medium mb-1"
            >{{ $t('wizard.course_step.name_pt') }} *</label
          >
          <input
            type="text"
            v-model="newUc.nome"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1"
            >{{ $t('wizard.course_step.name_en') }} *</label
          >
          <input
            type="text"
            v-model="newUc.name"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1"
            >{{ $t('wizard.course_step.acronym') }} *</label
          >
          <input
            type="text"
            v-model="newUc.sigla"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">{{ $t('wizard.uc_step.ects') }} *</label>
          <input
            type="number"
            v-model.number="newUc.ects"
            step="1"
            min="0"
            inputmode="numeric"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1"
            >{{ $t('wizard.uc_step.semester') }} *</label
          >
          <input
            type="number"
            v-model.number="newUc.semestre"
            step="1"
            min="0"
            inputmode="numeric"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">{{ $t('wizard.uc_step.year') }} *</label>
          <input
            type="number"
            v-model.number="newUc.ano"
            step="1"
            min="0"
            inputmode="numeric"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div class="col-span-2">
          <label class="block text-sm font-medium mb-2">{{
            $t('wizard.uc_step.workload_title')
          }}</label>
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-neutral-200 dark:border-neutral-700">
              <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                  <th class="px-2 py-2 text-left font-medium">
                    {{ $t('wizard.uc_step.workload_total') }}
                  </th>
                  <th class="px-2 py-2 text-left font-medium">T</th>
                  <th class="px-2 py-2 text-left font-medium">TP</th>
                  <th class="px-2 py-2 text-left font-medium">PL</th>
                  <th class="px-2 py-2 text-left font-medium">TC</th>
                  <th class="px-2 py-2 text-left font-medium">S</th>
                  <th class="px-2 py-2 text-left font-medium">E</th>
                  <th class="px-2 py-2 text-left font-medium">OT</th>
                  <th class="px-2 py-2 text-left font-medium">O</th>
                  <th class="px-2 py-2 text-left font-medium">
                    {{ $t('wizard.uc_step.contact_total') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr class="bg-white dark:bg-[#1a1a1a]">
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.horas_trabalho_totais"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-12 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.t"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-6 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.tp"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-6 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.pl"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-6 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.tc"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-6 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.s"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-6 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.e"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-6 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.ot"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-6 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      v-model.number="newUc.o"
                      step="1"
                      min="0"
                      inputmode="numeric"
                      placeholder="0"
                      class="w-full min-w-6 rounded-md border p-2 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                  <td class="p-2">
                    <input
                      type="number"
                      :value="workloadTotal"
                      readonly
                      class="w-full min-w-12 rounded-md border p-2 bg-neutral-50 dark:bg-neutral-900/40 dark:text-white dark:border-neutral-700 no-spin"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div
        class="mt-4 p-3 bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900 text-blue-800 dark:text-blue-300 rounded-md text-sm flex gap-3"
      >
        <font-awesome-icon :icon="['fas', 'info-circle']" class="text-lg mt-0.5 shrink-0" />
        <p>{{ $t('wizard.uc_step.optional_info') }}</p>
      </div>

      <button
        @click="addNewUc"
        class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 cursor-pointer"
      >
        {{ $t('wizard.uc_step.create_add') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { inject, ref, watch, onMounted, computed } from 'vue'
import SearchableDropdown from '../SearchableDropdown.vue'

const props = defineProps(['modelValue', 'courseData'])
const emit = defineEmits(['update:modelValue'])
const api = inject('api')

const ucs = ref([])
const dbUcs = ref([])
const selectedExistingUc = ref(null)
const newUcs = ref(0)
const loadingUcs = ref(false)

const newUc = ref({
  id: 0,
  nome: '',
  name: '',
  sigla: '',
  ects: null,
  horas_trabalho_totais: null,
  t: null,
  tp: null,
  pl: null,
  tc: null,
  s: null,
  e: null,
  ot: null,
  o: null,
  horas: null,
  semestre: null,
  ano: null
})

const workloadTotal = computed(() => {
  const values = [
    newUc.value.t,
    newUc.value.tp,
    newUc.value.pl,
    newUc.value.tc,
    newUc.value.s,
    newUc.value.e,
    newUc.value.ot,
    newUc.value.o
  ]
  const sum = values.reduce((acc, val) => acc + (Number.isFinite(val) ? val : 0), 0)
  return sum || 0
})

const normalizeString = (str) => {
  return str ? str.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : ''
}

const fetchUcs = async () => {
  const courseId = props.courseData?.data?.id
  if (!courseId) return // Skip fetching if no course is selected or if creating a new course

  loadingUcs.value = true
  try {
    const response = await api.get('/ucs', {
      params: {
        curso_id: courseId
      }
    })
    const data = response.data
    dbUcs.value = data.data || data
  } catch (err) {
    console.error('Error fetching UCs:', err)
  } finally {
    loadingUcs.value = false
  }
}

onMounted(() => {
  fetchUcs()
  if (Array.isArray(props.modelValue)) {
    ucs.value = [...props.modelValue]
  }
})

watch(
  ucs,
  () => {
    emit('update:modelValue', ucs.value)
  },
  { deep: true }
)

const addExistingUc = () => {
  if (selectedExistingUc.value) {
    // Avoid duplicates
    if (
      !ucs.value.find(
        (u) =>
          (u.id && u.id === selectedExistingUc.value.id) ||
          u.sigla === selectedExistingUc.value.sigla
      )
    ) {
      ucs.value.push({ ...selectedExistingUc.value, isNew: false })
    }
    selectedExistingUc.value = null
  }
}

const addNewUc = () => {
  if (newUc.value.nome && newUc.value.sigla) {
    newUcs.value++
    const ucPayload = { ...newUc.value, id: -newUcs.value, isNew: true }
    if (ucPayload.horas_trabalho_totais != null && ucPayload.horas == null) {
      ucPayload.horas = ucPayload.horas_trabalho_totais
    }
    ucs.value.push(ucPayload)
    // Reset form
    newUc.value = {
      id: 0,
      nome: '',
      name: '',
      sigla: '',
      ects: null,
      horas_trabalho_totais: null,
      t: null,
      tp: null,
      pl: null,
      tc: null,
      s: null,
      e: null,
      ot: null,
      o: null,
      horas: null,
      semestre: null,
      ano: null
    }
  }
}

const removeUc = (index) => {
  ucs.value.splice(index, 1)
}
</script>

<style scoped>
.no-spin::-webkit-outer-spin-button,
.no-spin::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.no-spin {
  -moz-appearance: textfield;
}
</style>
