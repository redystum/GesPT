<template>
  <header
    class="sticky top-0 z-50 border-b border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900"
  >
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center justify-between gap-4">
          <router-link to="/" class="flex items-center gap-3">
            <span
              class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-900 dark:text-neutral-100"
              >GP</span
            >
            <div class="leading-tight">
              <p class="text-sm sm:text-base font-semibold text-neutral-900 dark:text-neutral-100">
                {{ $t('brand.name') }}
              </p>
              <p
                class="text-[0.68rem] sm:text-xs uppercase tracking-[0.16em] text-neutral-500 dark:text-neutral-400"
              >
                {{ $t('brand.institution') }}
              </p>
            </div>
          </router-link>

          <div class="flex items-center gap-2 lg:hidden">
            <button
              @click="toggleLanguage"
              class="inline-flex h-9 items-center justify-center gap-2 rounded-md border border-neutral-200 dark:border-neutral-700 px-3 text-sm font-medium transition-colors duration-150 bg-white dark:bg-neutral-900 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800"
              :aria-label="$t('nav.language_toggle')"
            >
              <font-awesome-icon icon="fa-solid fa-language" class="mr-1" />
              <span>{{ locale.toUpperCase() }}</span>
            </button>

            <button
              @click="toggleTheme"
              class="inline-flex h-9 items-center justify-center gap-2 rounded-md border border-neutral-200 dark:border-neutral-700 px-3 text-sm font-medium transition-colors duration-150 bg-white dark:bg-neutral-900 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800 w-9 px-0"
              :aria-label="isDark ? $t('nav.theme_light') : $t('nav.theme_dark')"
            >
              <font-awesome-icon
                :icon="isDark ? ['fas', 'sun'] : ['fas', 'moon']"
                class="text-sm"
              />
            </button>

            <div v-if="authStore.isLoggedIn" class="relative ml-1" ref="mobileDropdownRef">
              <div
                @click="toggleMobileDropdown"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[color:var(--border)] bg-[color:var(--surface-soft)] text-xs font-semibold text-[color:var(--muted)] cursor-pointer"
              >
                {{ authStore.initials || 'U' }}
              </div>

              <!-- Dropdown for logout -->
              <div
                v-show="isMobileDropdownOpen"
                class="absolute right-0 top-full mt-1 w-32 rounded-md bg-white dark:bg-neutral-900 shadow-lg border border-neutral-200 dark:border-neutral-800 z-50"
              >
                <button
                  @click="logout"
                  class="cursor-pointer w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-md"
                >
                  Logout
                </button>
              </div>
            </div>

            <router-link
              v-else
              to="/login"
              class="cursor-pointer ml-1 inline-flex h-9 items-center justify-center rounded-md bg-neutral-900 px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
            >
              {{ $t('nav.login') }}
            </router-link>
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
              class="cursor-pointer inline-flex h-9 items-center justify-center gap-2 rounded-md border border-neutral-200 dark:border-neutral-700 px-3 text-sm font-medium transition-colors duration-150 bg-white dark:bg-neutral-900 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800"
              :aria-label="$t('nav.language_toggle')"
            >
              <span class="text-xs">
                <font-awesome-icon icon="fa-solid fa-language" class="mr-1" />
              </span>
              <span>{{ locale.toUpperCase() }}</span>
            </button>

            <button
              @click="toggleTheme"
              class="cursor-pointer inline-flex h-9 items-center justify-center gap-2 rounded-md border border-neutral-200 dark:border-neutral-700 px-3 text-sm font-medium transition-colors duration-150 bg-white dark:bg-neutral-900 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-50 dark:hover:bg-neutral-800 w-9 px-0"
              :aria-label="isDark ? $t('nav.theme_light') : $t('nav.theme_dark')"
            >
              <font-awesome-icon
                :icon="isDark ? ['fas', 'sun'] : ['fas', 'moon']"
                class="text-sm"
              />
            </button>

            <div v-if="authStore.isLoggedIn" class="relative ml-1" ref="dropdownRef">
              <div
                @click="toggleDropdown"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[color:var(--border)] bg-[color:var(--surface-soft)] text-xs font-semibold text-[color:var(--muted)] cursor-pointer"
              >
                {{ authStore.initials || 'U' }}
              </div>

              <!-- Dropdown for logout -->
              <div
                v-show="isDropdownOpen"
                class="absolute right-0 top-full mt-1 w-32 rounded-md bg-white dark:bg-neutral-900 shadow-lg border border-neutral-200 dark:border-neutral-800"
              >
                <button
                  @click="logout"
                  class="cursor-pointer w-full text-left px-4 py-2 text-sm text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-md"
                >
                  Logout
                </button>
              </div>
            </div>

            <router-link
              v-else
              to="/login"
              class="cursor-pointer ml-1 inline-flex h-9 items-center justify-center rounded-md bg-neutral-900 px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
            >
              {{ $t('nav.login') }}
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/i18n'
import { useTheme } from '@/composables/useTheme'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const LOCALE_STORAGE_KEY = 'gespt.locale'
const { locale, t } = useI18n()
const { isDark, toggleTheme } = useTheme()
const authStore = useAuthStore()

const isDropdownOpen = ref(false)
const dropdownRef = ref(null)
const isMobileDropdownOpen = ref(false)
const mobileDropdownRef = ref(null)

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const toggleMobileDropdown = () => {
  isMobileDropdownOpen.value = !isMobileDropdownOpen.value
}

const closeDropdown = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isDropdownOpen.value = false
  }
  if (mobileDropdownRef.value && !mobileDropdownRef.value.contains(event.target)) {
    isMobileDropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
})

const navLinks = computed(() => {
  const links = [
    { name: t('nav.home'), path: '/' },
    { name: t('nav.compare'), path: '/compare' }
  ]

  if (authStore.isLoggedIn) {
    if (authStore.hasPermission('planos_estudo.create')) {
      links.push({ name: t('nav.wizard'), path: '/wizard' })
    }
    const hasAdminAccess = authStore.hasPermission([
      'roles.view_any',
      'users.view_any',
      'login_dominios.view_any'
    ])
    if (hasAdminAccess) {
      links.push({ name: t('nav.admin'), path: '/admin' })
    }
  }

  return links
})

const toggleLanguage = () => {
  locale.value = locale.value === 'pt' ? 'en' : 'pt'
  localStorage.setItem(LOCALE_STORAGE_KEY, locale.value)
}

const logout = () => {
  authStore.logout()
  isDropdownOpen.value = false
  isMobileDropdownOpen.value = false
  router.push('/login')
}
</script>