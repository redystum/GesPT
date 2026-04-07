<template>
  <div class="page-container page-section animate-fade-in-up">
    <div class="mb-8">
      <p class="section-kicker">{{ $t('wizard.kicker') }}</p>
      <h1 class="page-title mt-2">{{ $t('wizard.title') }}</h1>
      <p class="page-subtitle">{{ $t('wizard.subtitle') }}</p>
    </div>

    <div class="mb-8 flex flex-wrap items-center gap-3 text-sm font-medium">
      <div class="status-pill">1 · {{ $t('wizard.step_course') }}</div>
      <div class="status-pill">2 · {{ $t('wizard.step_plan_info') }}</div>
      <div class="status-pill">3 · {{ $t('wizard.step_modules') }}</div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <section class="simple-card p-6 sm:p-8 lg:col-span-2">
        <h2 class="mb-6 text-2xl font-bold">{{ $t('wizard.configuration_title') }}</h2>

        <div class="space-y-5">
          <div>
            <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.plan_name') }}</label>
            <input type="text" :placeholder="$t('wizard.plan_name_placeholder')" class="simple-input" />
          </div>

          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.effective_date') }}</label>
              <input type="date" class="simple-input" />
            </div>

            <div>
              <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.expiration_date') }}</label>
              <input type="date" class="simple-input" />
            </div>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.institutional_code') }}</label>
            <input type="text" :placeholder="$t('wizard.code_placeholder')" class="simple-input font-mono" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-semibold">{{ $t('wizard.description') }}</label>
            <textarea
              :placeholder="$t('wizard.description_placeholder')"
              rows="4"
              class="simple-textarea resize-none"
            ></textarea>
          </div>
        </div>

        <div class="mt-8 flex items-center justify-between border-t border-[color:var(--border)] pt-6">
          <button class="simple-button-outline">{{ $t('common.back') }}</button>
          <button class="simple-button">{{ $t('common.next') }}</button>
        </div>
      </section>

      <aside class="space-y-5">
        <section class="simple-card p-6">
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
              <span class="status-pill">{{ $t('wizard.summary_status_value') }}</span>
            </div>
          </div>
        </section>
      </aside>
    </div>
  </div>
</template>
