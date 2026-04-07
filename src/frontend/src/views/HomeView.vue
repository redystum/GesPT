<template>
  <div class="page-container page-section animate-fade-in-up">
    <section class="simple-card p-6 sm:p-8 mb-8">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-3xl">
          <p class="section-kicker">{{ $t('home.kicker') }}</p>
          <h1 class="page-title mt-2">{{ $t('home.title') }}</h1>
          <p class="page-subtitle">{{ $t('home.subtitle') }}</p>
        </div>

        <div class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto">
          <div class="relative w-full sm:w-80">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[color:var(--muted)]">
              ⌕
            </span>
            <input
              type="text"
              :placeholder="$t('home.search')"
              class="simple-input pl-9"
              v-model="searchInput"
              @input="search"
            />
          </div>

          <button class="simple-button whitespace-nowrap">
            <span class="text-base leading-none">+</span>
            {{ $t('home.new_course') }}
          </button>
        </div>
      </div>
    </section>

    <div v-if="loading" class="state-box mb-6">
      <span class="mt-0.5 animate-pulse">•</span>
      <p>{{ $t('common.loading') }}</p>
    </div>

    <div v-else-if="error" class="state-box state-box-danger mb-6">
      <span class="mt-0.5">!</span>
      <p>{{ error }}</p>
    </div>

    <div v-else-if="courses.length === 0" class="state-box mb-6">
      <span class="mt-0.5">i</span>
      <div>
        <p class="font-semibold text-[color:var(--text)]">{{ $t('states.empty_title') }}</p>
        <p class="text-sm text-[color:var(--muted)]">{{ $t('home.empty_hint') }}</p>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <article
        v-for="course in courses"
        :key="course.id"
        class="simple-card interactive-card p-6 flex flex-col"
        style="animation: fade-in-up 0.45s ease-out backwards"
        :style="{ animationDelay: `${(course.id % 4) * 55}ms` }"
      >
        <router-link :to="{ path: '/compare', query: { course: course.id } }" class="mb-3 block">
          <div class="mb-3 flex items-start justify-between gap-2">
            <span class="status-pill">{{ course.sigla }} · {{ course.codigo }}</span>
          </div>

          <h3 class="text-lg font-bold leading-tight">
            {{ locale === 'pt' ? course.nome : course.name }}
          </h3>
        </router-link>

        <div class="mb-4 flex flex-wrap gap-2">
          <router-link
            v-for="plano in course.planos_estudo"
            :to="{ path: '/compare', query: { course: course.id, plan: plano.id } }"
            :key="plano.id"
            class="inline-flex items-center rounded-md border border-[color:var(--border)] px-2.5 py-1 text-xs font-medium text-[color:var(--text)] transition-colors duration-150 hover:text-[color:var(--primary)]"
          >
            {{ plano.ano }}
          </router-link>
        </div>

        <div class="border-t border-[color:var(--border)] pt-3 text-sm text-[color:var(--muted)]">
          {{ course.planos_estudo.length }} {{ $t('home.planos') }}
        </div>
      </article>
    </div>
  </div>
</template>

<script setup>
import { inject, ref } from 'vue'
import { useI18n } from '@/i18n'

const { locale } = useI18n()
const API = inject('apiURL')

const courses = ref([])
const searchInput = ref('')
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

    fetch(`${API}/cursos?search=${searchInput.value}&with_planos_estudo=1`)
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
  }, 450)
}
</script>
