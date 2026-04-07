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

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[16rem,1fr]">
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
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
          <div class="space-y-6 xl:col-span-1">
            <article
              class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6"
            >
              <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em]">
                <font-awesome-icon :icon="['fas', 'keyboard']" class="text-xs" />
                {{ $t('admin.rule_title') }}
              </h2>
              <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em]">
                {{ $t('admin.rule_pattern_label') }}
              </label>
              <input
                type="text"
                value="^[a-zA-Z0-9._%+-]+@ipleiria\.pt$"
                class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 mb-4 font-mono text-xs"
              />

              <div
                class="rounded-md border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-3 mb-4 text-xs"
              >
                <span class="font-semibold">{{ $t('admin.rule_behavior_label') }}</span>
                {{ $t('admin.rule_behavior') }}
              </div>

              <button
                class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 font-semibold transition-colors duration-150 border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 w-full text-xs"
              >
                {{ $t('admin.test_pattern') }}
              </button>
            </article>

            <article
              class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6"
            >
              <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em]">
                {{ $t('admin.system_policies') }}
              </h2>

              <div class="space-y-4 text-sm">
                <div
                  class="flex items-center justify-between border-b border-neutral-400 dark:border-neutral-600 pb-4"
                >
                  <span>{{ $t('admin.policy_mfa') }}</span>
                  <span
                    class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300"
                    >{{ $t('admin.status_active') }}</span
                  >
                </div>
                <div
                  class="flex items-center justify-between border-b border-neutral-400 dark:border-neutral-600 pb-4"
                >
                  <span>{{ $t('admin.policy_sso') }}</span>
                  <span
                    class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300"
                    >{{ $t('admin.status_connected') }}</span
                  >
                </div>
                <div class="flex items-center justify-between">
                  <span>{{ $t('admin.policy_bruteforce') }}</span>
                  <span
                    class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300"
                    >{{ $t('admin.status_pending') }}</span
                  >
                </div>
              </div>
            </article>
          </div>

          <article
            class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 overflow-hidden xl:col-span-2"
          >
            <div
              class="flex flex-col gap-4 border-b border-neutral-400 dark:border-neutral-600 p-5 sm:flex-row sm:items-center sm:justify-between"
            >
              <div>
                <h2 class="text-lg font-semibold">{{ $t('admin.whitelist_title') }}</h2>
                <p class="text-sm">{{ $t('admin.whitelist_subtitle') }}</p>
              </div>
              <div class="flex w-full gap-3 sm:w-auto">
                <input
                  type="text"
                  :placeholder="$t('admin.whitelist_search')"
                  class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 sm:w-64"
                />
                <button
                  class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300 whitespace-nowrap"
                >
                  <font-awesome-icon :icon="['fas', 'plus']" class="text-sm leading-none" />{{
                    $t('common.add_entry')
                  }}
                </button>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left min-w-155 whitespace-nowrap">
                <thead>
                  <tr
                    class="border-b border-neutral-200 dark:border-neutral-800 text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800/50"
                  >
                    <th class="px-5 py-3">{{ $t('admin.table_identifier') }}</th>
                    <th class="px-5 py-3">{{ $t('admin.table_role') }}</th>
                    <th class="px-5 py-3 text-right">{{ $t('admin.table_actions') }}</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 text-sm">
                  <tr class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                    <td class="px-5 py-4">
                      <p class="font-semibold text-neutral-900 dark:text-neutral-100">
                        @ipleiria.pt
                      </p>
                      <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                        {{ $t('admin.verified_domain') }}
                      </p>
                    </td>
                    <td class="px-5 py-4">
                      <span
                        class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300"
                        >{{ $t('admin.role_faculty') }}</span
                      >
                    </td>
                    <td class="px-5 py-4 text-right">
                      <button
                        class="text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors p-2"
                      >
                        <font-awesome-icon :icon="['fas', 'ellipsis-vertical']" class="text-sm" />
                      </button>
                    </td>
                  </tr>
                  <tr class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                    <td class="px-5 py-4">
                      <p class="font-semibold text-neutral-900 dark:text-neutral-100">
                        dean@ipleiria.pt
                      </p>
                      <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                        {{ $t('admin.individual_account') }}
                      </p>
                    </td>
                    <td class="px-5 py-4">
                      <span
                        class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300"
                        >{{ $t('admin.role_admin') }}</span
                      >
                    </td>
                    <td class="px-5 py-4 text-right">
                      <button
                        class="text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors p-2"
                      >
                        <font-awesome-icon :icon="['fas', 'ellipsis-vertical']" class="text-sm" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div
              class="flex items-center justify-between border-t border-neutral-400 dark:border-neutral-600 p-4 text-xs"
            >
              <span>{{ $t('admin.table_count') }}</span>
              <div class="flex items-center gap-3">
                <button class="transition-colors" disabled>
                  <font-awesome-icon :icon="['fas', 'chevron-left']" class="text-[10px]" />
                  {{ $t('common.previous') }}
                </button>
                <button class="transition-colors">
                  {{ $t('common.next') }}
                  <font-awesome-icon :icon="['fas', 'chevron-right']" class="text-[10px]" />
                </button>
              </div>
            </div>
          </article>
        </div>
      </section>
    </div>
  </div>
</template>
