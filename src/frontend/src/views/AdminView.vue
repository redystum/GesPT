<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up">
    <header class="mb-8 md:flex md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">
          {{ $t('admin.kicker') }}
        </p>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
          {{ $t('admin.title') }}
        </h1>
        <p class="mt-2 text-sm sm:text-base text-neutral-500 dark:text-neutral-400">
          {{ $t('admin.subtitle') }}
        </p>
      </div>
    </header>

    <div class="flex flex-col xl:flex-row gap-8 lg:gap-10">
      <aside class="w-full xl:w-64 shrink-0 xl:border-r border-neutral-200 dark:border-neutral-800 xl:pr-8">
        <nav class="flex xl:flex-col gap-2 overflow-x-auto pb-5 xl:pb-0 hide-scrollbar border-b border-neutral-200 dark:border-neutral-800 xl:border-b-0">
          <a
            href="#"
            class="flex items-center whitespace-nowrap rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 hover:text-neutral-900 dark:hover:text-neutral-100"
          >
            {{ $t('admin.menu_dashboard') }}
          </a>
          <a
            href="#"
            class="flex items-center whitespace-nowrap rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 hover:text-neutral-900 dark:hover:text-neutral-100"
          >
            {{ $t('admin.menu_curriculum') }}
          </a>
          <a
            href="#"
            class="flex items-center whitespace-nowrap rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 shadow-sm border border-neutral-200 dark:border-neutral-800"
          >
            {{ $t('admin.menu_auth') }}
          </a>
          <a
            href="#"
            class="flex items-center whitespace-nowrap rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 hover:text-neutral-900 dark:hover:text-neutral-100"
          >
            {{ $t('admin.menu_audit') }}
          </a>
        </nav>
      </aside>

      <main class="flex-1 min-w-0">
        <LoginView />
      </main>
    </div>
  </div>
</template>

<script setup>
import LoginView from '@/components/Admin/LoginView.vue'
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
