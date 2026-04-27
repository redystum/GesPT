<template>
  <div
    class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up"
  >
    <div class="mb-8 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
      <div>
        <p
          class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400"
        >
          {{ $t('admin.kicker') }}
        </p>
        <h1
          class="text-2xl sm:text-3xl font-bold leading-tight text-neutral-900 dark:text-neutral-100 mt-2"
        >
          {{ $t('admin.title') }}
        </h1>
        <p class="mt-2 text-sm sm:text-base text-neutral-500 dark:text-neutral-400">
          {{ $t('admin.subtitle') }}
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <button
          class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800"
        >
          {{ $t('common.discard') }}
        </button>
        <button
          class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300"
        >
          {{ $t('admin.save_configuration') }}
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[16rem_1fr]">
      <aside
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-4 sm:p-5"
      >
        <nav class="space-y-1.5">
          <a
            href="#"
            class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 w-full"
            >{{ $t('admin.menu_dashboard') }}</a
          >
          <a
            href="#"
            class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 w-full"
            >{{ $t('admin.menu_curriculum') }}</a
          >
          <a
            href="#"
            class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-900 dark:text-neutral-100 bg-neutral-200 dark:bg-neutral-800 w-full"
            >{{ $t('admin.menu_auth') }}</a
          >
          <a
            href="#"
            class="inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 w-full"
            >{{ $t('admin.menu_audit') }}</a
          >
        </nav>
      </aside>

      <section class="space-y-6">
        <LoginView />
      </section>
    </div>
  </div>
</template>

<script>
import LoginView from '@/components/Admin/LoginView.vue'

export default {
  components: { LoginView },
  data() {
    return {
      rule_behavior: '', // Bind this to the textarea
      regexPattern: /^[a-zA-Z0-9._%+-]+@ipleiria\.pt$/, // Example regex pattern
      validationResults: [] // Store validation results for each line
    }
  },
  watch: {
    rule_behavior(newVal) {
      this.validateLines(newVal)
    }
  },
  methods: {
    validateLines(text) {
      const lines = text.split('\n')
      this.validationResults = lines.map((line) => {
        const regex = new RegExp(this.regexPattern)
        return regex.test(line)
      })
    }
  }
}
</script>
