<template>
  <div>
    <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">Course Selection</h2>

    <div class="space-y-4">
      <div v-if="!isCreatingNew">
        <label class="block text-sm font-medium mb-1">Select Course</label>
        <select v-model="selectedCourseId" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700">
          <option value="" disabled>Select a course</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">
            {{ course.nome }} ({{ course.sigla }})
          </option>
        </select>

        <button @click="isCreatingNew = true" class="mt-4 text-blue-500 hover:underline text-sm cursor-pointer">+ Or create a new course</button>
      </div>

      <div v-else class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Name (PT)</label>
          <input type="text" v-model="newCourse.nome" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Name (EN)</label>
          <input type="text" v-model="newCourse.name" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Sigla</label>
          <input type="text" v-model="newCourse.sigla" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Code</label>
          <input type="text" v-model="newCourse.codigo" class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700" />
        </div>

        <button @click="isCreatingNew = false" class="text-blue-500 hover:underline text-sm cursor-pointer">Cancel creating new course</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, ref, watch, onMounted } from 'vue'

const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

const isCreatingNew = ref(false)
const selectedCourseId = ref('')
const newCourse = ref({ nome: '', name: '', sigla: '', codigo: '' })
const api = inject('api')

// Mock existing courses for UI demo, should fetch from API
const courses = ref([])
const loadingCourses = ref(false)
const errorCourses = ref('')

const fetchCourses = async () => {
  loadingCourses.value = true
  errorCourses.value = ''
  try {
    const response = await api.get('/cursos')
    const data = response.data
    // API might return data in a standard format or as paginated data.
    // Adjusting for common Laravel resource responses:
    courses.value = data.data || data
  } catch (err) {
    console.error(err)
    errorCourses.value = 'Could not load courses from server'
  } finally {
    loadingCourses.value = false
  }
}

onMounted(() => {
  fetchCourses()
  if (props.modelValue) {
    if (props.modelValue.isNew) {
      isCreatingNew.value = true
      newCourse.value = { ...props.modelValue.data }
    } else {
      selectedCourseId.value = props.modelValue.data?.id || ''
    }
  }
})

watch([isCreatingNew, selectedCourseId, newCourse, courses], () => {
  const selectedCourse = courses.value.find(c => c.id === selectedCourseId.value) || { id: selectedCourseId.value }
  emit('update:modelValue', {
    isNew: isCreatingNew.value,
    data: isCreatingNew.value ? newCourse.value : selectedCourse
  })
}, { deep: true })
</script>

