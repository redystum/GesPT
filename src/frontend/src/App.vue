<template>
  <div class="app-frame">
    <NavComponent />

    <main class="flex-1 z-10">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <footer class="mt-auto border-t border-[color:var(--border)] bg-[color:var(--surface)]">
      <div class="page-container py-7 sm:py-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
          <div class="space-y-2">
            <p class="text-base font-semibold">{{ $t('brand.name') }} · {{ $t('brand.institution') }}</p>
            <p class="max-w-xl text-sm text-[color:var(--muted)]">{{ $t('footer.description') }}</p>
            <p class="text-xs text-[color:var(--muted)]">
              © {{ currentYear }} {{ $t('brand.name') }}. {{ $t('footer.rights') }}
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-5">
            <a href="#" class="footer-link">{{ $t('footer.terms') }}</a>
            <a href="#" class="footer-link">{{ $t('footer.privacy') }}</a>
            <a href="#" class="footer-link">{{ $t('footer.support') }}</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, watchEffect } from 'vue'
import { useI18n } from '@/i18n'
import NavComponent from '@/components/Navbar/NavComponent.vue'

const { t } = useI18n()
const currentYear = computed(() => new Date().getFullYear())

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
