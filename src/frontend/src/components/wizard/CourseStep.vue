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

        <div
          class="mt-4 rounded-md border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/40 p-4"
        >
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
              <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                {{ $t('wizard.course_step.excel_template') }}
              </h3>
              <p class="text-xs text-neutral-600 dark:text-neutral-400">
                {{ $t('wizard.course_step.excel_template_hint') }}
              </p>
            </div>

            <button
              type="button"
              @click="downloadTemplate"
              :disabled="!selectedCourseId || downloadingTemplate"
              class="inline-flex items-center justify-center gap-2 rounded-md px-3 py-2 text-sm font-semibold transition-colors duration-150 bg-green-600 text-white border border-green-700 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed enabled:cursor-pointer"
            >
              <font-awesome-icon icon="fa-solid fa-file-excel" />
              {{
                downloadingTemplate
                  ? $t('wizard.course_step.downloading_template')
                  : $t('wizard.course_step.download_template')
              }}
            </button>
          </div>

          <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-3">
            <input
              type="file"
              accept=".xlsx,.xls,.csv"
              @change="importTemplate"
              :disabled="importingTemplate"
              class="block w-full text-sm text-neutral-700 dark:text-neutral-300 file:mr-3 file:rounded-md file:border-0 file:bg-neutral-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white dark:file:bg-neutral-200 dark:file:text-neutral-900 disabled:opacity-50"
            />
            <span v-if="importingTemplate" class="text-sm text-neutral-500 dark:text-neutral-400">
              {{ $t('wizard.course_step.importing_template') }}
            </span>
          </div>

          <p v-if="templateMessage" class="mt-3 text-sm text-green-700 dark:text-green-400">
            {{ templateMessage }}
          </p>
          <p v-if="templateError" class="mt-3 text-sm text-red-600 dark:text-red-400">
            {{ templateError }}
          </p>
        </div>

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
const emit = defineEmits(['update:modelValue', 'template-imported'])

const isCreatingNew = ref(false)
const selectedCourseId = ref('')
const newCourse = ref({ nome: '', name: '', sigla: '', codigo: '' })
const api = inject('api')
const { t } = useI18n()

// Mock existing courses for UI demo, should fetch from API
const courses = ref([])
const loadingCourses = ref(false)
const errorCourses = ref('')
const downloadingTemplate = ref(false)
const importingTemplate = ref(false)
const templateMessage = ref('')
const templateError = ref('')

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

const getDownloadFilename = (contentDisposition) => {
  const fallback = `course-template-${selectedCourseId.value}.xlsx`
  const match = contentDisposition?.match(/filename="?([^"]+)"?/)
  return match?.[1] || fallback
}

const downloadTemplate = async () => {
  if (!selectedCourseId.value) return

  downloadingTemplate.value = true
  templateMessage.value = ''
  templateError.value = ''

  try {
    const response = await api.get(`/wizard/course-template/${selectedCourseId.value}`, {
      responseType: 'blob'
    })

    const blobUrl = URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = blobUrl
    link.download = getDownloadFilename(response.headers['content-disposition'])
    document.body.appendChild(link)
    link.click()
    link.remove()
    URL.revokeObjectURL(blobUrl)
  } catch (err) {
    console.error(err)
    templateError.value = t('wizard.course_step.download_template_error')
  } finally {
    downloadingTemplate.value = false
  }
}

const importTemplate = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  importingTemplate.value = true
  templateMessage.value = ''
  templateError.value = ''

  try {
    const formData = new FormData()
    formData.append('file', file)
    if (selectedCourseId.value) {
      formData.append('course_id', selectedCourseId.value)
    }

    const response = await api.post('/wizard/course-template/import', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    const importedData = response.data?.data || response.data
    if (importedData?.course) {
      emit('template-imported', importedData)
    }

    templateMessage.value = response.data?.message || t('wizard.course_step.import_template_success')
  } catch (err) {
    console.error(err)
    templateError.value = err.response?.data?.message || t('wizard.course_step.import_template_error')
  } finally {
    importingTemplate.value = false
    event.target.value = ''
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
