<template>
  <div
    :class="{ dark: isDark }"
    class="min-h-screen flex flex-col font-sans transition-colors duration-200 bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100"
  >
    <NavComponent />

    <main class="flex-grow z-10 relative">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <footer
      class="border-t border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 py-8 mt-auto text-sm text-neutral-500 dark:text-neutral-400"
    >
      <div
        class="max-w-screen-2xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4"
      >
        <div>
          <p class="font-bold text-neutral-900 dark:text-neutral-100 mb-1">The Academic Curator</p>
          <p>© 2024. All rights reserved.</p>
        </div>
        <div class="flex gap-6">
          <a href="#" class="hover:text-neutral-900 dark:hover:text-white transition-colors"
            >Terms</a
          >
          <a href="#" class="hover:text-neutral-900 dark:hover:text-white transition-colors"
            >Privacy</a
          >
          <a href="#" class="hover:text-neutral-900 dark:hover:text-white transition-colors"
            >Support</a
          >
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import NavComponent from '@/components/Navbar/NavComponent.vue'
</script>
<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
