<template>
  <header class="sticky top-0 z-50 border-b border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center justify-between gap-4">
          <router-link to="/" class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-900 dark:text-neutral-100">GP</span>
            <div class="leading-tight">
              <p class="text-sm sm:text-base font-semibold text-neutral-900 dark:text-neutral-100">{{ $t('brand.name') }}</p>
              <p class="text-[0.68rem] sm:text-xs uppercase tracking-[0.16em] text-neutral-500 dark:text-neutral-400">{{ $t('brand.institution') }}</p>
            </div>
          </router-link>

          <div class="flex items-center gap-2 lg:hidden">
            <button
              @click="toggleLanguage"
              class="inline-flex h-9 items-center justify-center gap-2 rounded-md border border-neutral-200 dark:border-neutral-700 px-3 text-sm font-medium transition-colors duration-150 bg-white dark:bg-neutral-900 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800"
              :aria-label="$t('nav.language_toggle')"
            >
              <span class="text-xs">Aa</span>
              <span>{{ locale.toUpperCase() }}</span>
            </button>

            <button
              @click="toggleTheme"
              class="inline-flex h-9 items-center justify-center gap-2 rounded-md border border-neutral-200 dark:border-neutral-700 px-3 text-sm font-medium transition-colors duration-150 bg-white dark:bg-neutral-900 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800 w-9 px-0"
              :aria-label="isDark ? $t('nav.theme_light') : $t('nav.theme_dark')"
            >
              <span class="text-sm leading-none">{{ isDark ? '☀' : '☾' }}</span>
            </button>
          </div>
        </div>

        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-end lg:gap-4">
          <nav class="flex flex-wrap items-center gap-1 sm:gap-1.5">
            <router-link
              v-for="link in navLinks"
              :key="link.path"
              :to="link.path"
              class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800"
              active-class="text-neutral-900 dark:text-neutral-100 bg-neutral-200 dark:bg-neutral-800"
            >
              {{ link.name }}
            </router-link>
          </nav>

          <div class="hidden items-center gap-2 lg:flex">
            <button
              @click="toggleLanguage"
              class="inline-flex h-9 items-center justify-center gap-2 rounded-md border border-neutral-200 dark:border-neutral-700 px-3 text-sm font-medium transition-colors duration-150 bg-white dark:bg-neutral-900 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800"
              :aria-label="$t('nav.language_toggle')"
            >
              <span class="text-xs">Aa</span>
              <span>{{ locale.toUpperCase() }}</span>
            </button>

            <button
              @click="toggleTheme"
              class="inline-flex h-9 items-center justify-center gap-2 rounded-md border border-neutral-200 dark:border-neutral-700 px-3 text-sm font-medium transition-colors duration-150 bg-white dark:bg-neutral-900 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800 w-9 px-0"
              :aria-label="isDark ? $t('nav.theme_light') : $t('nav.theme_dark')"
            >
              <span class="text-sm leading-none">{{ isDark ? '☀' : '☾' }}</span>
            </button>

            <div class="ml-1 inline-flex h-9 w-9 items-center justify-center rounded-full border border-[color:var(--border)] bg-[color:var(--surface-soft)] text-xs font-semibold text-[color:var(--muted)]">
              IP
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from '@/i18n'
import { useTheme } from '@/composables/useTheme'

const LOCALE_STORAGE_KEY = 'gespt.locale'
const { locale, t } = useI18n()
const { isDark, toggleTheme } = useTheme()

const navLinks = computed(() => [
  { name: t('nav.catalog'), path: '/' },
  { name: t('nav.admin'), path: '/admin' },
  { name: t('nav.compare'), path: '/compare' },
  { name: t('nav.wizard'), path: '/wizard' },
  { name: t('nav.login'), path: '/login' }
])

const toggleLanguage = () => {
  locale.value = locale.value === 'pt' ? 'en' : 'pt'
  localStorage.setItem(LOCALE_STORAGE_KEY, locale.value)
}
</script>
