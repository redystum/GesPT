<template>
  <div class="relative min-h-screen flex flex-col overflow-x-hidden text-neutral-900 dark:text-neutral-100 bg-neutral-50 dark:bg-neutral-950">
    <NavComponent />

    <main class="flex-1 z-10">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <FooterView/>

  </div>
</template>

<script setup>
import { watchEffect, watch, inject } from 'vue'
import { useI18n } from '@/i18n'
import { useTheme } from '@/composables/useTheme'
import { useAuthStore } from '@/stores/auth'
import NavComponent from '@/components/Navbar/NavComponent.vue'
import FooterView from '@/components/Footer/FooterView.vue'

const { t, locale } = useI18n()
const { isDark } = useTheme()
const authStore = useAuthStore()
const api = inject('api')

watchEffect(() => {
  document.title = t('brand.app_title')
})

watch([locale, isDark], async ([newLocale, newIsDark]) => {
  if (authStore.isLoggedIn) {
    const themeValue = newIsDark ? 'dark' : 'light'
    if (!authStore.user || authStore.user.lang !== newLocale || authStore.user.theme !== themeValue) {
      try {
        const response = await api.put('/me/preferences', {
          lang: newLocale,
          theme: themeValue
        })
        if (response.data && response.data.user) {
          authStore.user = response.data.user
          localStorage.setItem('user', JSON.stringify(response.data.user))
        }
      } catch (error) {
        console.error('Failed to sync user preferences to DB:', error)
      }
    }
  }
})
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
