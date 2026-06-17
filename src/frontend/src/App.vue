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
import { watchEffect } from 'vue'
import { useI18n } from '@/i18n'
import NavComponent from '@/components/Navbar/NavComponent.vue'
import FooterView from '@/components/Footer/FooterView.vue'

const { t } = useI18n()

watchEffect(() => {
  document.title = t('brand.app_title')
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
