<template>
  <div>
    <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">
      {{ $t('wizard.course_step.title') }}
    </h2>

    <div class="space-y-4">
      <div v-if="!isCreatingNew">
        <label class="block text-sm font-medium mb-1">
          {{ $t('wizard.course_step.select_course') }}
          <span v-if="loadingCourses" class="text-sm text-neutral-500 font-normal ml-2">{{
            $t('common.loading')
          }}</span>
        </label>

        <SearchableDropdown
          v-model="selectedCourseId"
          :options="courses"
          :display-fn="(c) => `${c.nome} (${c.sigla})`"
          :search-fn="
            (c, q) =>
              normalizeString(c.nome?.toLowerCase() || '').includes(q) ||
              normalizeString(c.sigla?.toLowerCase() || '').includes(q)
          "
          :placeholder="$t('wizard.course_step.search_course_placeholder')"
        />

        <button
          @click="isCreatingNew = true"
          class="mt-4 text-blue-500 hover:underline text-sm cursor-pointer"
        >
          {{ $t('wizard.course_step.or_create') }}
        </button>
      </div>

      <div v-else class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">{{
            $t('wizard.course_step.name_pt')
          }}</label>
          <input
            type="text"
            v-model="newCourse.nome"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">{{
            $t('wizard.course_step.name_en')
          }}</label>
          <input
            type="text"
            v-model="newCourse.name"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">{{
            $t('wizard.course_step.acronym')
          }}</label>
          <input
            type="text"
            v-model="newCourse.sigla"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">{{ $t('wizard.course_step.code') }}</label>
          <input
            type="text"
            v-model="newCourse.codigo"
            class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          />
        </div>

        <button
          @click="isCreatingNew = false"
          class="text-blue-500 hover:underline text-sm cursor-pointer"
        >
          {{ $t('wizard.course_step.cancel_create') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, ref, watch, onMounted } from 'vue'
import { useI18n } from '@/i18n'
import SearchableDropdown from '../SearchableDropdown.vue'

const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

const isCreatingNew = ref(false)
const selectedCourseId = ref('')
const newCourse = ref({ nome: '', name: '', sigla: '', codigo: '' })
const api = inject('api')
const { t } = useI18n()

// Mock existing courses for UI demo, should fetch from API
const courses = ref([])
const loadingCourses = ref(false)
const errorCourses = ref('')

const normalizeString = (str) => {
  return str ? str.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : ''
}

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
    errorCourses.value = t('wizard.course_step.load_error')
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

watch(
  [isCreatingNew, selectedCourseId, newCourse, courses],
  () => {
    const selectedCourse = courses.value.find((c) => c.id === selectedCourseId.value) || {
      id: selectedCourseId.value
    }
    emit('update:modelValue', {
      isNew: isCreatingNew.value,
      data: isCreatingNew.value ? newCourse.value : selectedCourse
    })
  },
  { deep: true }
)
</script>
