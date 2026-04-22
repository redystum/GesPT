<template>
  <div>
    <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">Add Units (UCs)</h2>

    <div class="mb-4">
      <h3 class="font-semibold text-lg mb-2">Current UCs in Plan</h3>
      <div v-if="ucs.length === 0" class="text-neutral-500 italic">No UCs added yet.</div>
      <ul class="space-y-2">
        <li v-for="(uc, index) in ucs" :key="index" class="p-3 border rounded-md flex justify-between items-center bg-neutral-50 dark:bg-neutral-800 dark:border-neutral-700">
          <div>
            <div class="font-medium">{{ uc.nome }} ({{ uc.sigla }})</div>
            <div class="text-xs text-neutral-600 dark:text-neutral-400">ECTS: {{ uc.ects }} | Hours: {{ uc.horas }} | Semester: {{ uc.semestre }}</div>
          </div>
          <button @click="removeUc(index)" class="text-red-500 hover:text-red-700 text-sm cursor-pointer">Remove</button>
        </li>
      </ul>
    </div>

    <div class="border-t pt-4 border-neutral-200 dark:border-neutral-700">
      <h3 class="font-semibold text-lg mb-4">Add a New UC</h3>

      <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-md border border-blue-100 dark:border-blue-900">
        <label class="block text-sm font-medium mb-1 text-blue-800 dark:text-blue-300">
          Select an existing UC <span v-if="loadingUcs" class="text-sm text-blue-500 font-normal ml-2">Loading...</span>
        </label>
        <div class="flex gap-2">
          <div class="relative flex-1">
            <SearchableDropdown
              v-model="selectedExistingUc"
              :options="dbUcs"
              :return-object="true"
              :display-fn="u => `${u.nome} (${u.sigla})`"
              :search-fn="(u, q) => normalizeString(u.nome?.toLowerCase() || '').includes(q) || normalizeString(u.sigla?.toLowerCase() || '').includes(q)"
              placeholder="Search and select a UC..."
            />
          </div>
          <button @click="addExistingUc" :disabled="!selectedExistingUc" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed enabled:cursor-pointer">Add Selected</button>
        </div>
      </div>

      <div class="relative py-2">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-neutral-200 dark:border-neutral-700"></div></div>
        <div class="relative flex justify-center"><span class="bg-white dark:bg-[#1a1a1a] px-2 text-xs text-neutral-500 uppercase">Or create a new one</span></div>
      </div>

      <div class="grid grid-cols-2 gap-4 mt-4">
        <div>
          <label class="block text-sm font-medium mb-1">Name (PT)</label>
          <input type="text" v-model="newUc.nome" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Name (EN)</label>
          <input type="text" v-model="newUc.name" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Sigla</label>
          <input type="text" v-model="newUc.sigla" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">ECTS</label>
          <input type="number" v-model="newUc.ects" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Hours</label>
          <input type="number" v-model="newUc.horas" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Semester</label>
          <input type="number" v-model="newUc.semestre" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
      </div>
      <button @click="addNewUc" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 cursor-pointer">Create & Add UC</button>
    </div>
  </div>
</template>

<script setup>
import { inject, ref, watch, onMounted } from 'vue'
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
  horas: null,
  semestre: null
})

const normalizeString = (str) => {
  return str ? str.normalize('NFD').replace(/[\u0300-\u036f]/g, "") : ''
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

watch(ucs, () => {
  emit('update:modelValue', ucs.value)
}, { deep: true })

const addExistingUc = () => {
  if (selectedExistingUc.value) {
    // Avoid duplicates
    if (!ucs.value.find(u => (u.id && u.id === selectedExistingUc.value.id) || u.sigla === selectedExistingUc.value.sigla)) {
      ucs.value.push({ ...selectedExistingUc.value, isNew: false })
    }
    selectedExistingUc.value = null
  }
}

const addNewUc = () => {
  if (newUc.value.nome && newUc.value.sigla) {
    newUcs.value++
    ucs.value.push({ ...newUc.value, id: -newUcs.value, isNew: true })
    // Reset form
    newUc.value = { id: 0, nome: '', name: '', sigla: '', ects: null, horas: null, semestre: null }
  }
}

const removeUc = (index) => {
  ucs.value.splice(index, 1)
}
</script>
