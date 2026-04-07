<template>
  <div class="page-container page-section animate-fade-in-up">
    <div class="mb-8 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="section-kicker">{{ $t('admin.kicker') }}</p>
        <h1 class="page-title mt-2">{{ $t('admin.title') }}</h1>
        <p class="page-subtitle">{{ $t('admin.subtitle') }}</p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <button class="simple-button-outline">{{ $t('common.discard') }}</button>
        <button class="simple-button">{{ $t('admin.save_configuration') }}</button>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[16rem,1fr]">
      <aside class="simple-card p-4 sm:p-5">
        <nav class="space-y-1.5">
          <a href="#" class="nav-link w-full">{{ $t('admin.menu_dashboard') }}</a>
          <a href="#" class="nav-link w-full">{{ $t('admin.menu_curriculum') }}</a>
          <a href="#" class="nav-link nav-link-active w-full">{{ $t('admin.menu_auth') }}</a>
          <a href="#" class="nav-link w-full">{{ $t('admin.menu_audit') }}</a>
        </nav>
      </aside>

      <section class="space-y-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
          <div class="space-y-6 xl:col-span-1">
            <article class="simple-card p-6">
              <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em] text-[color:var(--muted)]">
                {{ $t('admin.validation_rule') }}
              </h2>

              <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-[color:var(--muted)]">
                {{ $t('admin.regex_pattern') }}
              </label>
              <input
                type="text"
                value="^[a-zA-Z0-9._%+-]+@ipleiria\.pt$"
                class="simple-input mb-4 font-mono text-xs"
              />

              <div class="subtle-panel mb-4 text-xs text-[color:var(--muted)]">
                <span class="font-semibold text-[color:var(--text)]">{{ $t('admin.rule_behavior_label') }}</span>
                {{ $t('admin.rule_behavior') }}
              </div>

              <button class="simple-button-outline w-full text-xs">{{ $t('admin.test_pattern') }}</button>
            </article>

            <article class="simple-card p-6">
              <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em] text-[color:var(--muted)]">
                {{ $t('admin.system_policies') }}
              </h2>

              <div class="space-y-4 text-sm">
                <div class="flex items-center justify-between border-b border-[color:var(--border)] pb-4">
                  <span>{{ $t('admin.policy_mfa') }}</span>
                  <span class="status-pill">{{ $t('admin.status_active') }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-[color:var(--border)] pb-4">
                  <span>{{ $t('admin.policy_sso') }}</span>
                  <span class="status-pill">{{ $t('admin.status_connected') }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>{{ $t('admin.policy_bruteforce') }}</span>
                  <span class="status-pill">{{ $t('admin.status_pending') }}</span>
                </div>
              </div>
            </article>
          </div>

          <article class="simple-card overflow-hidden xl:col-span-2">
            <div class="flex flex-col gap-4 border-b border-[color:var(--border)] p-5 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h2 class="text-lg font-semibold">{{ $t('admin.whitelist_title') }}</h2>
                <p class="text-sm text-[color:var(--muted)]">{{ $t('admin.whitelist_subtitle') }}</p>
              </div>

              <div class="flex w-full gap-3 sm:w-auto">
                <input
                  type="text"
                  :placeholder="$t('admin.filter_rules')"
                  class="simple-input text-sm sm:w-52"
                />
                <button class="simple-button whitespace-nowrap">{{ $t('common.add_entry') }}</button>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="data-table min-w-[620px]">
                <thead>
                  <tr>
                    <th>{{ $t('admin.table_identifier') }}</th>
                    <th>{{ $t('admin.table_role') }}</th>
                    <th class="text-right">{{ $t('admin.table_actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <p class="font-semibold">@ipleiria.pt</p>
                      <p class="text-xs text-[color:var(--muted)]">{{ $t('admin.verified_domain') }}</p>
                    </td>
                    <td>
                      <span class="status-pill">{{ $t('admin.role_faculty') }}</span>
                    </td>
                    <td class="text-right">
                      <button class="text-sm font-medium text-[color:var(--muted)] transition-colors hover:text-[color:var(--primary)]">
                        {{ $t('admin.edit') }}
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p class="font-semibold">dean@ipleiria.pt</p>
                      <p class="text-xs text-[color:var(--muted)]">{{ $t('admin.individual_account') }}</p>
                    </td>
                    <td>
                      <span class="status-pill">{{ $t('admin.role_admin') }}</span>
                    </td>
                    <td class="text-right">
                      <button class="text-sm font-medium text-[color:var(--muted)] transition-colors hover:text-[color:var(--primary)]">
                        {{ $t('admin.edit') }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex items-center justify-between border-t border-[color:var(--border)] p-4 text-xs text-[color:var(--muted)]">
              <span>{{ $t('admin.table_count') }}</span>
              <div class="flex items-center gap-3">
                <button class="transition-colors hover:text-[color:var(--text)]" disabled>
                  {{ $t('common.previous') }}
                </button>
                <button class="transition-colors hover:text-[color:var(--text)]">
                  {{ $t('common.next_page') }}
                </button>
              </div>
            </div>
          </article>
        </div>
      </section>
    </div>
  </div>
</template>
