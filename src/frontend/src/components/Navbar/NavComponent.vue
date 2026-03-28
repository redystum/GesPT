<template>
  <header
    class="sticky top-0 z-50 bg-white/80 dark:bg-neutral-950/80 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-800"
  >
    <div class="max-w-screen-2xl mx-auto px-6 h-16 flex items-center justify-between">
      <div class="flex items-center gap-8">
        <router-link to="/" class="flex items-center gap-3">
          <h1 class="text-lg font-bold tracking-tight text-neutral-900 dark:text-white">
            The Academic Curator
          </h1>
        </router-link>

        <nav class="hidden md:flex gap-6 text-sm font-medium">
          <template v-for="link in navLinks" :key="link.path">
            <router-link
              :to="link.path"
              class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors"
              active-class="text-neutral-900 dark:text-white font-semibold"
            >
              {{ link.name }}
            </router-link>
          </template>
        </nav>
      </div>

      <div class="flex items-center gap-4">
        <button
          @click="toggleLanguage"
          class="cursor-pointer p-1.5 flex items-center justify-center rounded-md text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
        >
          <font-awesome-icon icon="fa-solid fa-language" class="mr-1" />
          {{ locale === 'pt' ? 'PT' : 'EN' }}
        </button>

        <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-800 mx-1"></div>
        <button
          @click="toggleDark"
          class="cursor-pointer w-8 h-8 flex items-center justify-center rounded-md text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
          aria-label="Toggle Dark Mode"
        >
          <font-awesome-icon icon="fa-solid fa-sun" v-if="isDark" />
          <font-awesome-icon icon="fa-solid fa-moon" v-else />
        </button>

        <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-800 mx-1"></div>

        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 rounded-full bg-neutral-200 dark:bg-neutral-800 flex items-center justify-center text-xs font-semibold"
          >
            FA
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const isDark = ref(false)
const { locale } = useI18n()

onMounted(() => {
  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }
})

const navLinks = [
  { name: 'Course Catalog', path: '/' },
  { name: 'Admin', path: '/admin' },
  { name: 'Login', path: '/login' },
  { name: 'Compare', path: '/compare' },
  { name: 'Wizard', path: '/wizard' }
]
const toggleDark = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
}


const toggleLanguage = () => {
  locale.value = locale.value === 'pt' ? 'en' : 'pt'
}
</script>
