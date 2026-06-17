<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up">
    <section class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-neutral-100 dark:bg-[#1a1a1a] transition-colors duration-150 p-6 sm:p-8 mb-8">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-3xl">
          <p class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400">{{ $t('home.kicker') }}</p>
          <h1 class="text-2xl sm:text-3xl font-bold leading-tight text-neutral-900 dark:text-neutral-100 mt-2">{{ $t('home.title') }}</h1>
          <p class="mt-2 text-sm sm:text-base text-neutral-500 dark:text-neutral-400">{{ $t('home.subtitle') }}</p>
        </div>

        <div class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto" v-if="authStore.hasPermission('cursos.view_any') || authStore.hasPermission('cursos.create')">
          <div class="relative w-full sm:w-80" v-if="authStore.hasPermission('cursos.view_any')">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2">
              <font-awesome-icon :icon="['fas', 'search']" />
            </span>
            <input
              type="text"
              :placeholder="$t('home.search')"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 pl-9"
              v-model="searchInput"
              @input="search"
            />
          </div>

          <button
            v-if="authStore.hasPermission('cursos.create')"
            @click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300 whitespace-nowrap cursor-pointer"
          >
            <font-awesome-icon :icon="['fas', 'plus']" class="text-base leading-none" />
            {{ $t('home.new_course') }}
          </button>
        </div>
      </div>
    </section>

    <div v-if="!authStore.hasPermission('cursos.view_any')" class="flex flex-col items-center justify-center h-[300px] rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/30 p-6 text-center mb-6">
      <font-awesome-icon :icon="['fas', 'lock']" class="text-4xl text-neutral-400 mb-4" />
      <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">Access Denied</h3>
      <p class="mt-2 text-sm text-neutral-500 max-w-md">You do not have permission to view the course catalog.</p>
    </div>

    <div v-else-if="loading" class="flex items-start gap-3 rounded-md border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 mb-6">
      <font-awesome-icon :icon="['fas', 'circle-notch']" spin class="mt-0.5" />
      <p>{{ $t('common.loading') }}</p>
    </div>

    <div v-else-if="error" class="flex items-start gap-3 rounded-md border  dark:border-neutral-800 dark:bg-neutral-800/50 px-4 py-3 text-sm  dark:text-red-400 border-neutral-100 bg-neutral-600/10 text-red-500 mb-6">
      <font-awesome-icon :icon="['fas', 'exclamation-circle']" class="mt-0.5" />
      <p>{{ error }}</p>
    </div>

    <div v-else-if="courses.length === 0" class="flex items-start gap-3 rounded-md border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 mb-6">
      <font-awesome-icon :icon="['fas', 'info-circle']" class="mt-0.5" />
      <div>
        <p class="font-semibold">{{ $t('states.empty_title') }}</p>
        <p class="text-sm">{{ $t('home.empty_hint') }}</p>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <article
        v-for="course in courses"
        :key="course.id"
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-neutral-100 dark:bg-[#1a1a1a] transition-colors duration-150 hover:border-neutral-300 dark:hover:border-neutral-700 p-6 flex flex-col"
        style="animation: fade-in-up 0.45s ease-out backwards"
        :style="{ animationDelay: `${(course.id % 4) * 55}ms` }"
      >
        <router-link :to="{ name: 'course', params: { id: course.id } }" class="mb-3 block group">
          <div class="mb-3 flex items-start justify-between gap-2">
            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">{{ course.sigla }} · {{ course.codigo }}</span>
          </div>

          <h3 class="text-lg font-bold leading-tight group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors">
            {{ locale === 'pt' ? course.nome : course.name }}
          </h3>
        </router-link>

        <div class="mb-4 flex flex-wrap gap-2">
          <router-link
            v-for="plano in (course.planos_estudo || [])"
            :to="{ path: '/compare', query: { course: course.id, plan: plano.id } }"
            :key="plano.id"
            class="inline-flex items-center rounded-md border border-neutral-600 px-2.5 py-1 text-xs font-medium transition-colors duration-150 hover:text-[color:var(--primary)]"
          >
            {{ plano.ano }}
          </router-link>
        </div>

        <div class="border-t border-neutral-600 pt-3 flex justify-between items-center text-sm text-neutral-500">
          <span>{{ (course.planos_estudo || []).length }} {{ $t('home.planos') }}</span>
          <router-link :to="{ name: 'course', params: { id: course.id } }" class="text-xs font-semibold text-neutral-900 dark:text-neutral-200 hover:underline">
            {{ $t('course_detail.title') }} &rarr;
          </router-link>
        </div>
      </article>
    </div>
    
    <!-- CREATE COURSE MODAL -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="showCreateModal" @click.self="showCreateModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
          <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-2xl max-w-lg w-full overflow-hidden animate-zoom-in">
            <div class="px-6 py-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
              <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                {{ $t('home.new_course') }}
              </h3>
              <button @click="showCreateModal = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 cursor-pointer">
                <font-awesome-icon :icon="['fas', 'times']" />
              </button>
            </div>

            <form @submit.prevent="createCourse" class="p-6 space-y-4">
              <div v-if="createError" class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 rounded-lg text-xs text-red-600 dark:text-red-400">
                {{ createError }}
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                  {{ $t('course_detail.pt_name') }} *
                </label>
                <input
                  v-model="newCourseData.nome"
                  type="text"
                  required
                  class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                  {{ $t('course_detail.en_name') }} *
                </label>
                <input
                  v-model="newCourseData.name"
                  type="text"
                  class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.acronym') }} *
                  </label>
                  <input
                    v-model="newCourseData.sigla"
                    type="text"
                    required
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.code') }} *
                  </label>
                  <input
                    v-model="newCourseData.codigo"
                    type="text"
                    required
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                  />
                </div>
              </div>

              <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-end gap-3">
                <button
                  type="button"
                  @click="showCreateModal = false"
                  class="px-4 py-2 border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-md text-sm font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
                >
                  {{ $t('common.cancel') }}
                </button>
                <button
                  type="submit"
                  :disabled="creating"
                  class="px-4 py-2 bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 rounded-md text-sm font-semibold hover:bg-neutral-800 dark:hover:bg-neutral-200 disabled:opacity-50 transition-colors cursor-pointer"
                >
                  <font-awesome-icon v-if="creating" :icon="['fas', 'circle-notch']" spin class="mr-1.5 text-xs" />
                  {{ $t('common.save') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { inject, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/i18n'
import { useAuthStore } from '@/stores/auth'

const { locale } = useI18n()
const api = inject('api')
const router = useRouter()
const authStore = useAuthStore()

const showCreateModal = ref(false)
const creating = ref(false)
const createError = ref(null)
const newCourseData = ref({
  nome: '',
  name: '',
  sigla: '',
  codigo: ''
})

function openCreateModal() {
  createError.value = null
  newCourseData.value = {
    nome: '',
    name: '',
    sigla: '',
    codigo: ''
  }
  showCreateModal.value = true
}

async function createCourse() {
  creating.value = true
  createError.value = null
  try {
    const response = await api.post('/cursos', newCourseData.value)
    const createdCourse = response.data.data
    showCreateModal.value = false
    router.push({ name: 'course', params: { id: createdCourse.id } })
  } catch (err) {
    console.error('Failed to create course:', err)
    createError.value = err.response?.data?.message || 'An error occurred while creating the course.'
  } finally {
    creating.value = false
  }
}

const courses = ref([])
const searchInput = ref('')
const loading = ref(true)
const error = ref(null)
let searchTimeout = null

if (authStore.hasPermission('cursos.view_any')) {
  api
    .get('/cursos', {
      params: {
        with_planos_estudo: 1
      }
    })
    .then((data) => {
      courses.value = data.data.data
    })
    .catch((err) => {
      console.error('Error fetching courses:', err)
      error.value = err.response?.data?.message || err.message || 'An error occurred while fetching courses.'
    })
    .finally(() => {
      loading.value = false
    })
} else {
  loading.value = false
}

function search() {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  searchTimeout = setTimeout(() => {
    loading.value = true
    error.value = null

    api
      .get('/cursos', {
        params: {
          search: searchInput.value,
          with_planos_estudo: 1
        }
      })
      .then((data) => {
        courses.value = data.data.data
      })
      .catch((err) => {
        console.error('Error fetching courses:', err)
        error.value =
          err.response?.data?.message || err.message || 'An error occurred while fetching courses.'
      })
      .finally(() => {
        loading.value = false
      })
  }, 450)
}
</script>