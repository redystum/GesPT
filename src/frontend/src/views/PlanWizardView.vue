<template>
  <div class="max-w-7xl mx-auto px-6 py-10 animate-fade-in-up">
    <!-- Header -->
    <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100 mb-8 tracking-tight">
      {{ $t('wizard.title') }}
    </h1>

    <!-- Progress Stepper -->
    <div class="flex items-center gap-4 mb-10 text-sm font-medium">
      <div class="flex items-center gap-2 text-neutral-500 dark:text-neutral-400">
        <span
          class="w-6 h-6 rounded flex items-center justify-center border border-neutral-300 dark:border-neutral-700 text-xs"
          >1</span
        >
        {{ $t('wizard.step_course') }}
      </div>
      <div class="w-8 h-px bg-neutral-300 dark:bg-neutral-700"></div>
      <div class="flex items-center gap-2 text-neutral-900 dark:text-neutral-100">
        <span
          class="w-6 h-6 rounded flex items-center justify-center bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 text-xs"
          >2</span
        >
        {{ $t('wizard.step_plan_info') }}
      </div>
      <div class="w-8 h-px bg-neutral-300 dark:bg-neutral-700"></div>
      <div class="flex items-center gap-2 text-neutral-500 dark:text-neutral-400">
        <span
          class="w-6 h-6 rounded flex items-center justify-center border border-neutral-300 dark:border-neutral-700 text-xs"
          >3</span
        >
        {{ $t('wizard.step_modules') }}
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div
        class="lg:col-span-2 rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6 sm:p-8"
      >
        <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">
          {{ $t('wizard.configuration_title') }}
        </h2>

        <div class="space-y-5">
          <div>
            <label
              class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5"
              >{{ $t('wizard.plan_name') }}</label
            >
            <input
              type="text"
              :placeholder="$t('wizard.plan_name_placeholder')"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label
                class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5"
                >{{ $t('wizard.effective_date') }}</label
              >
              <input
                type="date"
                class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 appearance-none"
              />
            </div>
            <div>
              <label
                class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5"
                >{{ $t('wizard.expiration_date') }}</label
              >
              <input
                type="date"
                class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 appearance-none"
              />
            </div>
          </div>

          <div>
            <label
              class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5"
              >{{ $t('wizard.institutional_code') }}</label
            >
            <input
              type="text"
              :placeholder="$t('wizard.code_placeholder')"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 font-mono"
            />
          </div>

          <div>
            <label
              class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5"
              >{{ $t('wizard.description') }}</label
            >
            <textarea
              :placeholder="$t('wizard.description_placeholder')"
              rows="3"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 resize-none"
            ></textarea>
          </div>
        </div>

        <div class="mt-8 pt-6 border-t border-t-neutral-500 flex justify-between">
          <button
            class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800"
          >
            {{ $t('common.back') }}
          </button>
          <button
            class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300"
          >
            {{ $t('common.next') }}
          </button>
        </div>
      </div>

      <div class="space-y-6">
        <div
          class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6"
        >
          <h3 class="font-bold text-sm mb-4 text-neutral-900 dark:text-neutral-100">
            {{ $t('wizard.summary_title') }}
          </h3>
          <div class="space-y-3 text-sm">
            <div
              class="flex justify-between items-center pb-2 border-b text-neutral-600 dark:text-neutral-400"
            >
              <span>{{ $t('wizard.summary_course') }}</span>
              <span class="font-medium text-neutral-900 dark:text-neutral-100">{{
                $t('wizard.summary_course_value')
              }}</span>
            </div>
            <div
              class="flex justify-between items-center pb-2 border-b text-neutral-600 dark:text-neutral-400"
            >
              <span>{{ $t('wizard.summary_year') }}</span>
              <span class="font-medium text-neutral-900 dark:text-neutral-100">2026/2027</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-neutral-600 dark:text-neutral-400">{{
                $t('wizard.summary_status')
              }}</span>
              <span
                class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300"
                >{{ $t('wizard.summary_status_value') }}</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
