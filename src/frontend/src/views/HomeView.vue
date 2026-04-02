<template>
  <div class="max-w-screen-2xl mx-auto px-6 py-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-8 gap-6">
      <div class="shrink-0">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white mb-2 tracking-tight">
          {{ $t('home.title') }}
        </h1>
        <p class="text-neutral-500 dark:text-neutral-400 text-sm">
          {{ $t('home.subtitle') }}
        </p>
      </div>

      <!-- Action Bar -->
      <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
        <div class="relative w-full sm:w-72">
          <input
            type="text"
            :placeholder="$t('home.search')"
            class="simple-input w-full py-2 pr-4 border border-neutral-300 dark:border-neutral-700 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
            style="padding-left: 2.5rem;"
            v-model="search_input"
            @input="search"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-search text-neutral-400"></i>
          </div>
        </div>

        <button class="simple-button flex items-center justify-center gap-2 w-full sm:w-auto whitespace-nowrap shadow-sm">
          <i class="fas fa-plus"></i>
          New Course
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12 text-neutral-500">
      <i class="fas fa-spinner fa-spin text-2xl mb-4"></i>
      <p>Loading...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-lg mb-6 flex items-center gap-3 border border-red-200 dark:border-red-800/50">
      <i class="fas fa-exclamation-circle text-xl"></i>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="courses.length === 0" class="text-center py-12 text-neutral-500 bg-neutral-50 dark:bg-neutral-800/50 rounded-lg border border-neutral-200 dark:border-neutral-800">
      <i class="fas fa-search text-3xl mb-4 text-neutral-400"></i>
      <p class="text-lg">{{ $t('home.no_results') }}</p>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="course in courses"
        :key="course.id"
        class="simple-card p-6 flex flex-col hover:border-neutral-300 dark:hover:border-neutral-700"
        style="animation: fade-in-up 0.3s ease-out backwards"
        :style="{ animationDelay: `${(course.id % 4) * 50}ms` }"
      >
        <router-link :to="`/cursos/${course.id}`" class="mb-4 cursor-pointer">
          <div class="flex justify-between items-start mb-4">
            <span
              class="px-2 py-0.5 text-xs font-medium rounded bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400"
            >
              {{ course.sigla }} - {{ course.codigo }}
            </span>
          </div>

          <h3 class="font-bold text-neutral-900 dark:text-white text-lg leading-tight mb-2">
            {{ locale === 'pt' ? course.nome : course.name }}
          </h3>
        </router-link>

        <div class="mb-6 grow line-clamp-2 grid grid-cols-2 gap-6">
          <router-link
            v-for="plano in course.planos_estudo"
            :to="`/planos/${plano.id}`"
            :key="plano.id"
            class="text-sm text-neutral-500 dark:text-neutral-400 hover:underline"
          >
            {{ plano.ano }}
          </router-link>
        </div>

        <div
          class="flex items-center justify-between text-xs text-neutral-500 dark:text-neutral-400 border-t border-neutral-100 dark:border-neutral-800 pt-4 mt-auto"
        >
          <span class="flex items-center gap-1.5 font-medium">
            {{ course.planos_estudo.length }} {{ $t('home.planos') }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
let API = inject('apiURL')

const courses = ref([])
const search_input = ref("")
const loading = ref(true)
const error = ref(null)
let searchTimeout = null

fetch(`${API}/cursos?with_planos_estudo=1`)
  .then((response) => {
    if (!response.ok) throw new Error('Failed to load courses')
    return response.json()
  })
  .then((data) => {
    courses.value = data.data
  })
  .catch((err) => {
    console.error('Error fetching courses:', err)
    error.value = err.message || 'An error occurred while fetching courses.'
  })
  .finally(() => {
    loading.value = false
  })

function search() {
    if (searchTimeout) {
      clearTimeout(searchTimeout)
    }

    searchTimeout = setTimeout(() => {
      loading.value = true
      error.value = null
      fetch(`${API}/cursos?search=${search_input.value}&with_planos_estudo=1`)
        .then((response) => {
          if (!response.ok) throw new Error('Failed to load courses')
          return response.json()
        })
        .then((data) => {
          courses.value = data.data
        })
        .catch((err) => {
          console.error('Error fetching courses:', err)
          error.value = err.message || 'An error occurred while fetching courses.'
        })
        .finally(() => {
          loading.value = false
        })
    }, 500)
}
</script>
