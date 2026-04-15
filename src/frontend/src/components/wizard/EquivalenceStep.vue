<template>
  <div>
    <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">Equivalences</h2>

    <div class="mb-4">
      <label class="block text-sm font-medium mb-1">Previous Plan</label>
      <select v-model="selectedPrevPlanId" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700">
        <option value="" disabled>Select previous plan</option>
        <option v-for="plan in prevPlans" :key="plan.id" :value="plan.id">
          {{ plan.ano }} ({{ plan.curso?.nome || 'N/A' }})
        </option>
      </select>
    </div>

    <div v-if="selectedPrevPlanId" class="border-t pt-4 border-neutral-200 dark:border-neutral-700">
      <h3 class="font-semibold text-lg mb-2">Map Equivalences</h3>
      <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">Select existing UCs from previous plan that equate to UCs in the new plan.</p>

      <div v-for="(equiv, index) in equivalences" :key="index" class="mb-4 p-4 border rounded-md dark:border-neutral-700">
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-xs font-semibold mb-1">Old UC(s)</label>
            <select multiple v-model="equiv.oldUcs" class="w-full h-24 rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700 text-sm">
              <option v-for="oldUc in prevUcs" :key="oldUc.id" :value="oldUc.id">{{ oldUc.nome || oldUc.uc?.nome }}</option>
            </select>
          </div>
          <div class="flex items-center">
            <span class="text-2xl">👉</span>
          </div>
          <div class="flex-1">
            <label class="block text-xs font-semibold mb-1">New UC</label>
            <select v-model="equiv.newUc" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700 text-sm">
              <option value="" disabled>Select new UC</option>
              <option v-for="(newUc, i) in newPlanUcs" :key="i" :value="newUc.sigla || newUc.nome">{{ newUc.nome }}</option>
            </select>
          </div>
          <div class="flex items-center">
            <button @click="removeEquiv(index)" class="text-red-500 font-bold ml-2 cursor-pointer">X</button>
          </div>
        </div>
      </div>

      <button @click="addEquiv" class="mt-2 text-blue-500 hover:text-blue-700 text-sm font-medium cursor-pointer">+ Add Equivalence</button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  modelValue: { type: Object, default: () => ({ prevPlanId: '', equivalences: [] }) },
  newPlanUcs: { type: Array, default: () => [] },
  courseData: { type: Object, default: () => ({}) }
})
const emit = defineEmits(['update:modelValue'])

const selectedPrevPlanId = ref('')
const equivalences = ref([])

const prevPlans = ref([])
const prevUcs = ref([])

const fetchPrevPlans = async () => {
  try {
    const courseId = props.courseData?.data?.id
    if (!courseId) return

    const apiUrl = import.meta.env.VITE_API_PATH_URL || 'http://localhost:8000/api'
    const response = await fetch(`${apiUrl}/planos-estudo?curso_id=${courseId}`)
    if (!response.ok) throw new Error('Failed to load plans')
    const data = await response.json()
    prevPlans.value = data.data || data
  } catch (err) {
    console.error('Error fetching previous plans:', err)
  }
}

const fetchPrevPlanUcs = async (planId) => {
  try {
    const apiUrl = import.meta.env.VITE_API_PATH_URL || 'http://localhost:8000/api'
    const response = await fetch(`${apiUrl}/planos-estudo/${planId}`)
    if (!response.ok) throw new Error('Failed to load plan detail')
    const data = await response.json()
    const plan = data.data || data
    // Assuming backend returns related UCs in planoUcs or similar
    prevUcs.value = plan.planoUcs ? plan.planoUcs.map(pu => pu.uc || pu) : []
  } catch (err) {
    console.error('Error fetching plan UCs:', err)
    prevUcs.value = []
  }
}

onMounted(() => {
  fetchPrevPlans()

  if (props.modelValue) {
    selectedPrevPlanId.value = props.modelValue.prevPlanId || ''
    equivalences.value = [...(props.modelValue.equivalences || [])]
  }
})

watch(selectedPrevPlanId, (newId) => {
  if (newId) {
    fetchPrevPlanUcs(newId)
  } else {
    prevUcs.value = []
  }
})

watch([selectedPrevPlanId, equivalences], () => {
  emit('update:modelValue', {
    prevPlanId: selectedPrevPlanId.value,
    equivalences: equivalences.value
  })
}, { deep: true })

const addEquiv = () => {
  equivalences.value.push({ oldUcs: [], newUc: '' })
}

const removeEquiv = (index) => {
  equivalences.value.splice(index, 1)
}
</script>

