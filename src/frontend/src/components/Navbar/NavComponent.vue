<template>
  <header class="header-shell">
    <div class="page-container py-3 sm:py-4">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center justify-between gap-4">
          <router-link to="/" class="flex items-center gap-3">
            <span class="brand-mark">GP</span>
            <div class="brand-label">
              <p class="brand-title">{{ $t('brand.name') }}</p>
              <p class="brand-subtitle">{{ $t('brand.institution') }}</p>
            </div>
          </router-link>

          <div class="flex items-center gap-2 lg:hidden">
            <button
              @click="toggleLanguage"
              class="icon-button"
              :aria-label="$t('nav.language_toggle')"
            >
              <span class="text-xs">Aa</span>
              <span>{{ locale.toUpperCase() }}</span>
            </button>

            <button
              @click="toggleTheme"
              class="icon-button w-9 px-0"
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
              class="nav-link"
              active-class="nav-link-active"
            >
              {{ link.name }}
            </router-link>
          </nav>

          <div class="hidden items-center gap-2 lg:flex">
            <button
              @click="toggleLanguage"
              class="icon-button"
              :aria-label="$t('nav.language_toggle')"
            >
              <span class="text-xs">Aa</span>
              <span>{{ locale.toUpperCase() }}</span>
            </button>

            <button
              @click="toggleTheme"
              class="icon-button w-9 px-0"
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
