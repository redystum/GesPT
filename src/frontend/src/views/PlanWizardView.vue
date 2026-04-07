<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up">
    <div class="mb-8">
      <p class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400">{{ $t('wizard.kicker') }}</p>
      <h1 class="text-2xl sm:text-3xl font-bold leading-tight text-neutral-900 dark:text-neutral-100 mt-2">{{ $t('wizard.title') }}</h1>
      <p class="mt-2 text-sm sm:text-base text-neutral-500 dark:text-neutral-400">{{ $t('wizard.subtitle') }}</p>
    </div>

    <div class="mb-8 flex flex-wrap items-center gap-3 text-sm font-medium">
      <div class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">1 · {{ $t('wizard.step_course') }}</div>
      <div class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">2 · {{ $t('wizard.step_plan_info') }}</div>
      <div class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">3 · {{ $t('wizard.step_modules') }}</div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <section class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6 sm:p-8 lg:col-span-2">
        <h2 class="mb-6 text-2xl font-bold">{{ $t('wizard.configuration_title') }}</h2>

        <div class="space-y-5">
          <div>
            <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.plan_name') }}</label>
            <input type="text" :placeholder="$t('wizard.plan_name_placeholder')" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20" />
          </div>

          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.effective_date') }}</label>
              <input type="date" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20" />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.expiration_date') }}</label>
              <input type="date" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20" />
            </div>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.institutional_code') }}</label>
            <input type="text" :placeholder="$t('wizard.code_placeholder')" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 font-mono" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.description') }}</label>
            <textarea
              :placeholder="$t('wizard.description_placeholder')"
              rows="4"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 resize-none"
            ></textarea>
          </div>
        </div>

        <div class="mt-8 flex items-center justify-between border-t border-[color:var(--border)] pt-6">
          <button class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800">{{ $t('common.back') }}</button>
          <button class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300">{{ $t('common.next') }}</button>
        </div>
      </section>

      <aside class="space-y-5">
        <section class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6">
          <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em] text-[color:var(--muted)]">
            {{ $t('wizard.summary_title') }}
          </h3>

          <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between border-b border-[color:var(--border)] pb-2">
              <span class="text-[color:var(--muted)]">{{ $t('wizard.summary_course') }}</span>
              <span class="font-semibold">{{ $t('wizard.summary_course_value') }}</span>
            </div>
            <div class="flex items-center justify-between border-b border-[color:var(--border)] pb-2">
              <span class="text-[color:var(--muted)]">{{ $t('wizard.summary_year') }}</span>
              <span class="font-semibold">2026/2027</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-[color:var(--muted)]">{{ $t('wizard.summary_status') }}</span>
              <span class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300">{{ $t('wizard.summary_status_value') }}</span>
            </div>
          </div>
        </section>
      </aside>
    </div>
  </div>
</template>
