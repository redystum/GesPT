<template>
  <div
    :class="{ dark: isDark }"
    class="min-h-screen flex flex-col font-sans transition-colors duration-200 bg-neutral-50 dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100"
  >
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
            @click="toggleDark"
            class="w-8 h-8 flex items-center justify-center rounded-md text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
            aria-label="Toggle Dark Mode"
          >
            <!-- Sun Icon for Dark Mode (toggle to light) -->
            <svg
              v-if="isDark"
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 4.22a1 1 0 01-1.415 1.415l-.708-.708a1 1 0 011.415-1.415l.708.708zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-4.22 4.22a1 1 0 011.415 1.415l-.708.708a1 1 0 01-1.415-1.415l.708-.708zM10 18a1 1 0 01-1-1v-1a1 1 0 112 0v1a1 1 0 01-1 1zm-4.22-4.22a1 1 0 01-1.415-1.415l.708-.708a1 1 0 011.415 1.415l-.708.708zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm4.22-4.22a1 1 0 011.415-1.415l.708.708a1 1 0 01-1.415 1.415l-.708-.708zM10 6a4 4 0 100 8 4 4 0 000-8z"
                clip-rule="evenodd"
              />
            </svg>
            <!-- Moon Icon for Light Mode (toggle to dark) -->
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
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
import { ref, onMounted } from 'vue'

const isDark = ref(false)

const navLinks = [
  { name: 'Course Catalog', path: '/' },
  { name: 'Admin', path: '/admin' },
  { name: 'Login', path: '/login' },
  { name: 'Compare', path: '/compare' },
  { name: 'Wizard', path: '/wizard' },
]

onMounted(() => {
  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }
})

const toggleDark = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
}
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
