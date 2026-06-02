<style scoped></style>

<template>
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8 items-start">
    <!-- Rule Configuration -->
    <div class="xl:col-span-1 space-y-6">
      <section
        class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-sm flex flex-col p-5 sm:p-6"
      >
        <header class="mb-5">
          <h2
            class="text-sm font-bold uppercase tracking-wider text-neutral-900 dark:text-neutral-100"
          >
            {{ $t('admin.accepted_domains_title') }}
          </h2>
        </header>

        <div class="space-y-5 flex-1">
          <div>
            <label
              class="mb-2 block text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400"
            >
              {{ $t('admin.accepted_domains_label') }}
            </label>
            <div class="flex flex-col sm:flex-row gap-2">
              <input
                type="text"
                v-model="domainInput"
                :placeholder="$t('admin.accepted_domains_placeholder')"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow font-mono"
                @keydown.enter.prevent="addDomain"
              />
              <button
                class="inline-flex cursor-pointer items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-colors border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-[#1a1a1a] text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-neutral-900/10 dark:focus:ring-neutral-100/10"
                @click="addDomain"
              >
                {{ $t('common.add_entry') }}
              </button>
            </div>
            <p
              v-if="domainErrorKey"
              class="text-red-600 dark:text-red-400 text-xs font-medium mt-2"
            >
              {{ $t(domainErrorKey) }}
            </p>
          </div>

          <div v-if="domains.length" class="space-y-3">
            <div
              v-for="domainEntry in domains"
              :key="domainEntry.domain"
              class="rounded-lg border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-xs bg-neutral-50 dark:bg-neutral-900/50"
            >
              <div class="flex items-center justify-between gap-3">
                <span class="truncate font-mono">{{ domainEntry.domain }}</span>
                <button
                  class="inline-flex cursor-pointer items-center justify-center w-7 h-7 rounded-md text-neutral-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-neutral-400"
                  @click="removeDomain(domainEntry.domain)"
                  :disabled="domainEntry.id === 1 || domainEntry.domain === 'gespt.local'"
                  :title="$t('common.remove')"
                >
                  <font-awesome-icon :icon="['fas', 'trash']" class="text-xs" />
                </button>
              </div>
              <div class="mt-2">
                <label
                  class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400"
                >
                  {{ $t('admin.default_role_short_label') }}
                </label>
                <SearchableDropdown
                  v-model="domainEntry.default_role_id"
                  :options="availableRoles"
                  :displayFn="(role) => role.nome || role.name"
                  valueKey="id"
                  :placeholder="$t('admin.default_role_placeholder')"
                />
              </div>
            </div>
          </div>

          <div
            v-if="legacyRegex"
            class="text-[11px] text-neutral-500 dark:text-neutral-400 font-mono break-all bg-neutral-100 dark:bg-neutral-800/50 p-2 rounded"
          >
            <p class="font-semibold">{{ $t('admin.legacy_regex_note_title') }}</p>
            <p>{{ legacyRegex }}</p>
          </div>

          <div
            v-if="configError"
            class="text-red-600 dark:text-red-400 text-xs font-medium bg-red-50 dark:bg-red-900/10 p-3 rounded-md flex items-start gap-2"
          >
            <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="mt-0.5" />
            <span>{{ configError }}</span>
          </div>
        </div>
      </section>
    </div>

    <!-- Whitelist Table -->
    <section
      class="xl:col-span-2 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-sm flex flex-col h-[550px] sm:h-[650px]"
    >
      <header
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 border-b border-neutral-100 dark:border-neutral-800 shrink-0"
      >
        <div>
          <h2 class="text-base font-bold text-neutral-900 dark:text-neutral-100">
            {{ $t('admin.whitelist_title') }}
          </h2>
          <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
            {{ $t('admin.whitelist_subtitle') }}
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
          <div class="relative">
            <font-awesome-icon
              :icon="['fas', 'search']"
              class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"
            />
            <input
              type="text"
              v-model="searchQuery"
              :placeholder="$t('admin.whitelist_search')"
              class="w-full sm:w-64 rounded-lg border border-neutral-300 dark:border-neutral-700 pl-9 pr-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow"
            />
          </div>
          <button
            @click="showAddModal = true"
            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-200 shadow-sm whitespace-nowrap"
          >
            <font-awesome-icon :icon="['fas', 'plus']" class="text-sm" />
            {{ $t('common.add_entry') }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-x-auto overflow-y-auto">
        <table class="w-full text-left whitespace-nowrap text-sm">
          <thead
            class="sticky top-0 z-10 bg-neutral-50 dark:bg-[#1a1a1a] backdrop-blur-sm border-b border-neutral-200 dark:border-neutral-800"
          >
            <tr
              class="text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400"
            >
              <th class="px-6 py-4">{{ $t('admin.table_identifier') }}</th>
              <th class="px-6 py-4">{{ $t('admin.table_role') }}</th>
              <th class="px-6 py-4">
                {{ $t('admin.table_custom_permissions') || 'Custom Permissions' }}
              </th>
              <th class="px-6 py-4 text-right">{{ $t('admin.table_actions') }}</th>
            </tr>
          </thead>
          <tbody v-if="isLoading" class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <tr>
              <td colspan="4" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                <font-awesome-icon :icon="['fas', 'spinner']" spin class="mr-2 text-neutral-400" />
                {{ $t('admin.loading_users') }}
              </td>
            </tr>
          </tbody>
          <tbody
            v-else-if="users.length > 0"
            class="divide-y divide-neutral-100 dark:divide-neutral-800"
          >
            <tr
              v-for="user in users"
              :key="user.id"
              class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/30 group"
              :class="{ 'opacity-50': isUserBlocked(user) }"
            >
              <td class="px-6 py-3.5">
                <div class="flex items-center gap-2.5">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <p class="font-medium text-neutral-900 dark:text-neutral-100 truncate">
                        {{ user.name }}
                      </p>
                      <span
                        v-if="user.type === 'local'"
                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 tracking-wide uppercase"
                      >
                        {{ $t('admin.add_user_type_local') }}
                      </span>
                    </div>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate mt-0.5">
                      {{ user.email }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-3.5">
                <div class="flex flex-wrap gap-1.5">
                  <span
                    v-for="role in user.roles"
                    :key="role.id"
                    class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-300"
                  >
                    {{ role.nome }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-3.5">
                <span
                  class="font-medium"
                  :class="[
                    (user.custom_permissions ?? 0) === 0
                      ? 'opacity-40 text-neutral-500'
                      : 'text-neutral-900 dark:text-neutral-100'
                  ]"
                >
                  {{ user.custom_permissions ?? 0 }}
                </span>
              </td>
              <td class="px-6 py-3.5 text-right">
                <button
                  @click="editUser(user)"
                  :disabled="fetchingUserId === user.id || user.id === 1"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-md text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  :title="$t('admin.edit') || 'Edit'"
                >
                  <font-awesome-icon
                    v-if="fetchingUserId === user.id"
                    :icon="['fas', 'spinner']"
                    spin
                    class="text-sm"
                  />
                  <font-awesome-icon v-else :icon="['fas', 'pen']" class="text-sm" />
                </button>
                <button
                  @click="openBlockModal(user)"
                  :disabled="user.id === 1"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-md transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  :class="[
                    isUserBlocked(user)
                      ? 'text-red-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20'
                      : 'text-neutral-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20'
                  ]"
                  :title="isUserBlocked(user) ? ($t('admin.unblock') || 'Unblock') : ($t('admin.block') || 'Block')"
                >
                  <font-awesome-icon
                    :icon="['fas', isUserBlocked(user) ? 'unlock' : 'lock']"
                    class="text-sm"
                  />
                </button>
                <button
                  @click="confirmDelete(user)"
                  :disabled="user.id === 1"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-md text-neutral-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  :title="$t('common.delete') || 'Delete'"
                >
                  <font-awesome-icon :icon="['fas', 'trash']" class="text-sm" />
                </button>
              </td>
            </tr>
          </tbody>
          <tbody v-else class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <tr>
              <td colspan="4" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                {{ $t('admin.no_users') }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <footer
        class="flex items-center justify-between border-t border-neutral-200 dark:border-neutral-800 px-6 py-4 bg-neutral-50/50 dark:bg-neutral-800/10 shrink-0"
      >
        <span class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">
          {{ pagination.from || 0 }} - {{ pagination.to || 0 }} {{ $t('admin.of') }}
          {{ pagination.total || 0 }} {{ $t('admin.total_users') }}
        </span>
        <div class="flex items-center gap-2">
          <button
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            :disabled="!pagination.prev_page_url"
            @click="fetchPage(pagination.current_page - 1)"
          >
            <font-awesome-icon :icon="['fas', 'chevron-left']" class="text-xs" />
          </button>
          <button
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            :disabled="!pagination.next_page_url"
            @click="fetchPage(pagination.current_page + 1)"
          >
            <font-awesome-icon :icon="['fas', 'chevron-right']" class="text-xs" />
          </button>
        </div>
      </footer>
    </section>

    <!-- Add User Modal -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="showAddModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
      >
        <div
          class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm transition-opacity"
          @click="closeAddModal"
        ></div>
        <div
          class="relative bg-white dark:bg-[#1a1a1a] rounded-xl shadow-2xl w-full max-w-md border border-neutral-200 dark:border-neutral-800 flex flex-col overflow-hidden"
        >
          <header
            class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/20"
          >
            <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">
              {{ $t('admin.add_user_title') }}
            </h3>
            <button
              @click="closeAddModal"
              class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors outline-none rounded-md p-1 cursor-pointer"
            >
              <font-awesome-icon :icon="['fas', 'xmark']" />
            </button>
          </header>

          <div class="p-6 space-y-5 overflow-y-auto max-h-[calc(100vh-16rem)]">
            <div
              v-if="errors.general"
              class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/50 text-red-700 dark:text-red-400 rounded-lg text-sm font-medium flex items-start gap-2"
            >
              <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="mt-0.5" />
              <span>{{ errors.general }}</span>
            </div>

            <div class="space-y-4">
              <div>
                <label
                  class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300"
                  >{{ $t('admin.add_user_name') }}</label
                >
                <input
                  v-model="newUser.name"
                  @keydown.enter="addUser"
                  type="text"
                  placeholder="João Pedro"
                  :class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.name
                  }"
                  class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100"
                />
                <p
                  v-if="errors.name"
                  class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium"
                >
                  {{ errors.name[0] }}
                </p>
              </div>

              <div>
                <label
                  class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300"
                  >{{ $t('admin.add_user_email') }}</label
                >
                <input
                  v-model="newUser.email"
                  @keydown.enter="addUser"
                  type="email"
                  placeholder="joao.pedro@ipleiria.pt"
                  :class="{
                    'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.email,
                    'border-yellow-500 focus:border-yellow-500 focus:ring-yellow-500/20': warnings.email
                  }"
                  class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100"
                />
                <p
                  v-if="errors.email"
                  class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium"
                >
                  {{ errors.email[0] }}
                </p>
                <p
                  v-if="warnings.email"
                  class="text-yellow-600 dark:text-yellow-500 text-xs mt-1.5 font-medium"
                >
                  {{ $t(warnings.email[0]) }}
                </p>
              </div>

              <div>
                <label
                  class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300"
                  >{{ $t('admin.add_user_type') }}</label
                >
                <div class="relative">
                  <select
                    v-model="newUser.type"
                    :class="{
                      'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.type
                    }"
                    class="w-full appearance-none rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 cursor-pointer"
                  >
                    <option value="remote">{{ $t('admin.add_user_type_remote') }}</option>
                    <option value="local">{{ $t('admin.add_user_type_local') }}</option>
                  </select>
                  <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-500"
                  >
                    <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-xs" />
                  </div>
                </div>
                <p
                  v-if="errors.type"
                  class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium"
                >
                  {{ errors.type[0] }}
                </p>
              </div>

              <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
              >
                <div
                  v-if="newUser.type === 'local'"
                  class="space-y-4 pt-2 border-t border-neutral-100 dark:border-neutral-800"
                >
                  <div>
                    <label
                      class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300"
                      >{{ $t('admin.add_user_password') }}</label
                    >
                    <input
                      v-model="newUser.password"
                      @keydown.enter="addUser"
                      type="password"
                      placeholder="••••••••"
                      :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.password
                      }"
                      class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100"
                    />
                    <p
                      v-if="errors.password"
                      class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium"
                    >
                      {{ errors.password[0] }}
                    </p>
                  </div>
                  <div>
                    <label
                      class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300"
                      >{{ $t('admin.add_user_password_confirm') }}</label
                    >
                    <input
                      v-model="newUser.password_confirmation"
                      @keydown.enter="addUser"
                      type="password"
                      placeholder="••••••••"
                      class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100"
                    />
                  </div>
                  <div>
                    <label
                      class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300"
                      >{{ $t('admin.add_user_recovery_email') }}</label
                    >
                    <input
                      v-model="newUser.recovery_email"
                      @keydown.enter="addUser"
                      type="email"
                      placeholder="joao.pedro@ipleiria.pt"
                      :class="{
                        'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.recovery_email
                      }"
                      class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100"
                    />
                    <p
                      v-if="errors.recovery_email"
                      class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium"
                    >
                      {{ errors.recovery_email[0] }}
                    </p>
                  </div>
                </div>
              </Transition>
            </div>
          </div>

          <footer
            class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 px-6 py-4 border-t border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/20"
          >
            <button
              @click="closeAddModal"
              class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium transition-colors text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 bg-transparent hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg cursor-pointer"
            >
              {{ $t('common.cancel') }}
            </button>
            <button
              @click="addUser"
              :disabled="isAddingUser"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-medium transition-colors bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-900/20 dark:focus:ring-neutral-100/20 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm cursor-pointer"
            >
              <span v-if="isAddingUser">{{ $t('admin.saving') }}</span>
              <span v-else>{{ $t('admin.save') }}</span>
            </button>
          </footer>
        </div>
      </div>
    </Transition>

    <!-- Edit User Modal -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="showEditUserModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
      >
        <div
          class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm transition-opacity"
          @click="closeEditUserModal"
        ></div>
        <div
          class="relative w-full max-w-5xl max-h-[85vh] h-[750px] flex flex-col bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl overflow-hidden border border-neutral-200 dark:border-neutral-800"
        >
          <!-- Header -->
          <header
            class="flex items-center justify-between p-6 border-b border-neutral-100 dark:border-neutral-800 shrink-0"
          >
            <div>
              <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                {{ $t('admin.edit_user_title') || 'Edit User' }}
              </h3>
            </div>
            <button
              @click="closeEditUserModal"
              class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 transition-colors p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer"
            >
              <font-awesome-icon :icon="['fas', 'times']" />
            </button>
          </header>

          <!-- Body -->
          <div
            class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-neutral-50/30 dark:bg-[#1a1a1a]"
          >
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
              <!-- Left Column: User Details & Roles -->
              <div
                class="lg:col-span-1 lg:border-r border-neutral-100 dark:border-neutral-800 lg:pr-6"
              >
                <div class="lg:sticky lg:top-0 space-y-6">
                  <div>
                    <label
                      class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2"
                      >{{ $t('admin.add_user_name') }}</label
                    >
                    <input
                      v-model="editUserForm.name"
                      type="text"
                      class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow"
                    />
                  </div>

                  <div>
                    <h4
                      class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-800 pb-2"
                    >
                      {{ $t('admin.menu_roles') }}
                    </h4>
                    <div class="space-y-3">
                      <label
                        v-for="role in availableRoles"
                        :key="role.id"
                        class="flex items-center gap-3 cursor-pointer group"
                      >
                        <div class="relative flex items-center justify-center">
                          <input
                            type="checkbox"
                            :value="role.id"
                            :checked="editUserForm.roles.includes(role.id)"
                            @change="toggleRole(role.id)"
                            class="peer sr-only"
                          />
                          <div
                            class="w-5 h-5 rounded border-2 border-neutral-300 dark:border-neutral-600 peer-checked:bg-neutral-900 peer-checked:border-neutral-900 dark:peer-checked:bg-neutral-100 dark:peer-checked:border-neutral-100 transition-colors flex items-center justify-center peer-checked:[&>svg]:opacity-100"
                          >
                            <svg
                              class="text-white dark:text-neutral-900 w-3 h-3 opacity-0 transition-opacity"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="3"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                            >
                              <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                          </div>
                        </div>
                        <span
                          class="text-sm font-medium text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-neutral-100 transition-colors"
                          >{{ role.nome || role.name }}</span
                        >
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column: Permissions Categories -->
              <div class="lg:col-span-3 space-y-8">
                <div
                  class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/20 text-blue-700 dark:text-blue-400 p-3 rounded-lg flex items-start gap-3 text-sm"
                >
                  <font-awesome-icon :icon="['fas', 'circle-info']" class="mt-0.5" />
                  <p>
                    {{
                      $t('admin.role_permissions_warning') ||
                      'Permissions inherited from roles cannot be removed manually. You can assign additional custom permissions below.'
                    }}
                  </p>
                </div>

                <div v-for="category in availablePermissions" :key="category.id || category.nome">
                  <h4
                    class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-800 pb-2"
                  >
                    {{ category.nome || category.name }}
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label
                      v-for="perm in category.permissoes"
                      :key="perm.id"
                      class="flex items-start gap-3 cursor-pointer group p-3 rounded-lg border border-neutral-100 dark:border-neutral-800 hover:border-neutral-300 dark:hover:border-neutral-600 transition-colors bg-white dark:bg-[#1a1a1a] hover:bg-neutral-50 dark:hover:bg-neutral-800/50"
                      :class="{
                        'opacity-60 cursor-not-allowed': lockedPermissions.includes(perm.id)
                      }"
                    >
                      <div class="relative flex items-center justify-center mt-0.5">
                        <input
                          type="checkbox"
                          :value="perm.id"
                          :checked="
                            lockedPermissions.includes(perm.id) ||
                            editUserForm.custom_permissions.includes(perm.id)
                          "
                          :disabled="lockedPermissions.includes(perm.id)"
                          @change="toggleCustomPermission(perm.id)"
                          class="peer sr-only"
                        />
                        <div
                          class="w-5 h-5 rounded border-2 border-neutral-300 dark:border-neutral-600 peer-checked:bg-neutral-900 peer-checked:border-neutral-900 dark:peer-checked:bg-neutral-100 dark:peer-checked:border-neutral-100 peer-disabled:bg-neutral-300 dark:peer-disabled:bg-neutral-700 peer-disabled:border-neutral-300 dark:peer-disabled:border-neutral-700 transition-colors flex items-center justify-center peer-checked:[&>svg]:opacity-100"
                        >
                          <svg
                            class="text-white dark:text-neutral-900 w-3 h-3 opacity-0 transition-opacity"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <polyline points="20 6 9 17 4 12"></polyline>
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <span
                          class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-neutral-100 transition-colors"
                          >{{ perm.nome || perm.name }}</span
                        >
                        <p class="text-xs text-neutral-500 mt-1 leading-relaxed">
                          {{ perm.descricao || perm.description }}
                        </p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="editUserError"
            class="px-6 py-3 bg-red-50 dark:bg-red-900/10 border-t border-red-100 dark:border-red-900/20 text-red-600 dark:text-red-400 text-sm flex items-center gap-2 shrink-0"
          >
            <font-awesome-icon :icon="['fas', 'triangle-exclamation']" />
            {{ editUserError }}
          </div>

          <footer
            class="p-4 sm:p-6 border-t border-neutral-100 dark:border-neutral-800 flex justify-end gap-3 bg-neutral-50/80 dark:bg-neutral-900/30 shrink-0"
          >
            <button
              @click="closeEditUserModal"
              class="px-4 py-2 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
            >
              {{ $t('common.cancel') || 'Cancel' }}
            </button>
            <button
              @click="saveUser"
              class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg px-6 py-2 text-sm font-medium transition-colors bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-200 shadow-sm"
              :disabled="isEditingUser"
            >
              <font-awesome-icon
                v-if="isEditingUser"
                :icon="['fas', 'spinner']"
                spin
                class="text-sm"
              />
              {{ $t('admin.save') || 'Save' }}
            </button>
          </footer>
        </div>
      </div>
    </Transition>

    <!-- Delete Confirmation Modal -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
      >
        <div
          class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm"
          @click="closeDeleteModal"
        ></div>
        <div
          class="relative w-full max-w-sm bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl overflow-hidden border border-neutral-200 dark:border-neutral-800 p-6"
        >
          <div class="flex flex-col items-center text-center">
            <div
              class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4 text-red-600 dark:text-red-400"
            >
              <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="text-xl" />
            </div>
            <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-2">
              {{ $t('admin.delete_user_title') || 'Delete User' }}
            </h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6">
              {{
                $t('admin.delete_user_desc') ||
                'Are you sure you want to delete this user? This action cannot be undone.'
              }}
              <br /><br />
              <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{
                userToDelete?.name
              }}</span>
            </p>

            <div
              v-if="deleteError"
              class="w-full mb-6 px-4 py-3 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/20 rounded-lg text-red-600 dark:text-red-400 text-sm flex items-start text-left gap-2"
            >
              <font-awesome-icon :icon="['fas', 'circle-exclamation']" class="mt-0.5 shrink-0" />
              <span>{{ deleteError }}</span>
            </div>

            <div class="flex gap-3 w-full">
              <button
                @click="closeDeleteModal"
                class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors cursor-pointer"
              >
                {{ $t('common.cancel') || 'Cancel' }}
              </button>
              <button
                @click="deleteUser"
                class="flex-1 inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors bg-red-600 text-white hover:bg-red-700 shadow-sm"
                :disabled="isDeleting"
              >
                <font-awesome-icon v-if="isDeleting" :icon="['fas', 'spinner']" spin />
                {{ $t('admin.delete_user_confirm') || 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Block/Unblock Confirmation Modal -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showBlockModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
      >
        <div
          class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm"
          @click="closeBlockModal"
        ></div>
        <div
          class="relative w-full max-w-md bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl overflow-hidden border border-neutral-200 dark:border-neutral-800 p-6"
        >
          <div class="flex flex-col gap-4">
            <div class="flex items-start gap-3">
              <div
                class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400"
              >
                <font-awesome-icon :icon="['fas', blockAction === 'block' ? 'lock' : 'unlock']" />
              </div>
              <div>
                <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                  {{
                    blockAction === 'block'
                      ? ($t('admin.block_user_title') || 'Block User')
                      : ($t('admin.unblock_user_title') || 'Unblock User')
                  }}
                </h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                  {{ blockUser?.name }}
                </p>
              </div>
            </div>

            <div v-if="blockAction === 'block'" class="space-y-2">
              <label
                class="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300"
              >
                {{ $t('admin.block_reason_label') || 'Reason (optional)' }}
              </label>
              <textarea
                v-model="blockReasonInput"
                rows="3"
                class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100"
                :placeholder="$t('admin.block_reason_placeholder') || 'Add a reason for blocking'"
              ></textarea>
            </div>

            <div v-else class="text-sm text-neutral-600 dark:text-neutral-300">
              <p class="font-semibold text-neutral-800 dark:text-neutral-200">
                {{ $t('admin.block_reason_title') || 'Block reason' }}
              </p>
              <p class="mt-1">
                {{ blockReasonDisplay || ($t('admin.block_reason_empty') || 'No reason provided.') }}
              </p>
            </div>

            <div
              v-if="blockError"
              class="w-full px-4 py-3 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/20 rounded-lg text-red-600 dark:text-red-400 text-sm flex items-start gap-2"
            >
              <font-awesome-icon :icon="['fas', 'circle-exclamation']" class="mt-0.5 shrink-0" />
              <span>{{ blockError }}</span>
            </div>

            <div class="flex gap-3 w-full pt-2">
              <button
                @click="closeBlockModal"
                class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors cursor-pointer"
              >
                {{ $t('common.cancel') || 'Cancel' }}
              </button>
              <button
                @click="confirmBlockAction"
                class="flex-1 inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors bg-amber-600 text-white hover:bg-amber-700 shadow-sm"
                :disabled="isBlocking"
              >
                <font-awesome-icon v-if="isBlocking" :icon="['fas', 'spinner']" spin />
                {{
                  blockAction === 'block'
                    ? ($t('admin.block_confirm') || 'Block')
                    : ($t('admin.unblock_confirm') || 'Unblock')
                }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, inject, computed } from 'vue'
import { useI18n } from '@/i18n'
import SearchableDropdown from '../SearchableDropdown.vue'

const { t } = useI18n()
const api = inject('api')

const domains = ref([])
const domainInput = ref('')
const domainErrorKey = ref('')
const legacyRegex = ref('')
const configError = ref('')

const isSavingConfig = ref(false)

const users = ref([])
const searchQuery = ref('')
const isLoading = ref(true)
let searchTimeout = null

const showAddModal = ref(false)
const isAddingUser = ref(false)
const newUser = ref({ name: '', email: '', type: 'remote', password: '', password_confirmation: '', recovery_email: '' })
const errors = ref({})
const warnings = ref({})

const showEditUserModal = ref(false)
const isEditingUser = ref(false)
const fetchingUserId = ref(null)
const editUserError = ref(null)

const showDeleteModal = ref(false)
const userToDelete = ref(null)
const isDeleting = ref(false)
const deleteError = ref(null)

const showBlockModal = ref(false)
const isBlocking = ref(false)
const blockUser = ref(null)
const blockAction = ref('block')
const blockReasonInput = ref('')
const blockReasonDisplay = ref('')
const blockError = ref(null)

const editUserForm = ref({
  id: null,
  name: '',
  roles: [],
  custom_permissions: []
})

const availableRoles = ref([])
const availablePermissions = ref([])
const rolePermissionsCache = ref({})

const lockedPermissions = computed(() => {
  const locked = new Set()
  editUserForm.value.roles.forEach(roleId => {
    const perms = rolePermissionsCache.value[roleId]
    if (perms) {
      perms.forEach(pId => locked.add(pId))
    }
  })
  return Array.from(locked)
})

const fetchGlobalData = async () => {
  try {
    const [rolesRes, permsRes] = await Promise.all([
      api.get('/admin/roles/all'),
      api.get('/admin/permissions')
    ])
    availableRoles.value = rolesRes.data.data || rolesRes.data || []
    availablePermissions.value = permsRes.data.data || permsRes.data || []
  } catch (err) {
    console.error('Error fetching global data:', err)
  }
}

const normalizeDomain = (value) => value.trim().toLowerCase().replace(/^@/, '')
const isDomainValid = (value) => /^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?(?:\.[a-z0-9-]+)+$/.test(value)

const addDomain = () => {
  const normalized = normalizeDomain(domainInput.value)
  domainErrorKey.value = ''
  if (!normalized) {
    return
  }
  if (!isDomainValid(normalized)) {
    domainErrorKey.value = 'admin.invalid_domain'
    return
  }
  if (domains.value.some(entry => entry.domain === normalized)) {
    return
  }
  domains.value.push({ id: null, domain: normalized, default_role_id: null })
  domainInput.value = ''
}

const removeDomain = (domain) => {
  domains.value = domains.value.filter(item => item.domain !== domain)
}

const ensureRolePermissions = async (roleId) => {
  if (rolePermissionsCache.value[roleId]) return
  try {
    const res = await api.get(`/admin/roles/${roleId}`)
    const roleData = res.data.data || res.data
    const perms = (roleData.permissoes || roleData.permissions || []).map(p => p.id)
    rolePermissionsCache.value = {
      ...rolePermissionsCache.value,
      [roleId]: perms
    }
  } catch(e) {
    console.error(`Error fetching role ${roleId}:`, e)
  }
}

const toggleRole = async (roleId) => {
  const index = editUserForm.value.roles.indexOf(roleId)
  if (index === -1) {
    editUserForm.value.roles.push(roleId)
    await ensureRolePermissions(roleId)
  } else {
    editUserForm.value.roles.splice(index, 1)
  }
}

const toggleCustomPermission = (permId) => {
  if (lockedPermissions.value.includes(permId)) return
  const index = editUserForm.value.custom_permissions.indexOf(permId)
  if (index === -1) {
    editUserForm.value.custom_permissions.push(permId)
  } else {
    editUserForm.value.custom_permissions.splice(index, 1)
  }
}

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
  prev_page_url: null,
  next_page_url: null,
})

const fetchUsers = async (page = 1) => {
  isLoading.value = true
  try {
    let url = `/admin/users?page=${page}`
    if (searchQuery.value) {
      url += `&search=${encodeURIComponent(searchQuery.value)}`
    }
    const response = await api.get(url)
    users.value = response.data.data || []

    // Update pagination metadata from Laravel response
    if (response.data.meta) {
        pagination.value = {
            current_page: response.data.meta.current_page,
            last_page: response.data.meta.last_page,
            per_page: response.data.meta.per_page,
            total: response.data.meta.total,
            from: response.data.meta.from,
            to: response.data.meta.to,
            prev_page_url: response.data.links?.prev,
            next_page_url: response.data.links?.next,
        }
    } else {
        // Fallback if the response doesn't have meta/links wrapped
        pagination.value = {
            current_page: response.data.current_page || 1,
            last_page: response.data.last_page || 1,
            per_page: response.data.per_page || 15,
            total: response.data.total || users.value.length,
            from: response.data.from || (users.value.length ? 1 : 0),
            to: response.data.to || users.value.length,
            prev_page_url: response.data.prev_page_url,
            next_page_url: response.data.next_page_url,
        }
    }
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchUsers(page)
  }
}

const closeAddModal = () => {
  showAddModal.value = false
  newUser.value = { name: '', email: '', type: 'remote', password: '', password_confirmation: '', recovery_email: '' }
  errors.value = {}
  warnings.value = {}
}

const addUser = async () => {
  isAddingUser.value = true
  errors.value = {}
  warnings.value = {}
  try {
    const payload = { ...newUser.value }
    if (payload.type === 'local') {
        if (payload.email && !payload.email.endsWith('@gespt.local')) {
            const username = payload.email.includes('@') ? payload.email.split('@')[0] : payload.email
            newUser.value.email = `${username}@gespt.local`
            warnings.value = { email: ['admin.add_user_email_changed'] }
            isAddingUser.value = false
            return
        }
    } else {
      delete payload.password
      delete payload.password_confirmation
      delete payload.recovery_email
    }

    await api.post('/admin/users', payload)
    closeAddModal()
    fetchUsers()
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      if (error.response.data.message && !error.response.data.errors) {
         errors.value = { general: error.response.data.message }
      }
    } else {
      errors.value = { general: t('error.unexpected') }
      console.error('Error adding user:', error)
    }
  } finally {
    isAddingUser.value = false
  }
}

const editUser = async (user) => {
  fetchingUserId.value = user.id
  editUserError.value = null
  try {
    const response = await api.get(`/admin/users/${user.id}`)
    const userData = response.data.data || response.data

    editUserForm.value = {
      id: userData.id,
      name: userData.name,
      roles: (userData.roles || []).map(r => r.id),
      custom_permissions: (userData.custom_permissions || userData.permissoes_personalizadas || []).map(p => p.id)
    }

    await Promise.all(editUserForm.value.roles.map(rId => ensureRolePermissions(rId)))

    showEditUserModal.value = true
  } catch(e) {
    console.error('Error fetching user:', e)
  } finally {
    fetchingUserId.value = null
  }
}

const closeEditUserModal = () => {
  showEditUserModal.value = false
  editUserError.value = null
}

const saveUser = async () => {
  isEditingUser.value = true
  editUserError.value = null
  try {
    const payload = {
      name: editUserForm.value.name,
      roles: editUserForm.value.roles,
      custom_permissions: editUserForm.value.custom_permissions
    }
    await api.put(`/admin/users/${editUserForm.value.id}`, payload)

    await fetchUsers(pagination.value.current_page)
    closeEditUserModal()
  } catch (error) {
    console.error('Error saving user:', error)
    editUserError.value = error.response?.data?.message || t('error.failedToSaveUser')
  } finally {
    isEditingUser.value = false
  }
}

const confirmDelete = (user) => {
  userToDelete.value = user
  deleteError.value = null
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  userToDelete.value = null
  deleteError.value = null
}

const deleteUser = async () => {
  if (!userToDelete.value) return
  isDeleting.value = true
  deleteError.value = null
  try {
    await api.delete(`/admin/users/${userToDelete.value.id}`)
    await fetchUsers(pagination.value.current_page)
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting user:', error)
    deleteError.value = error.response?.data?.message || t('error.failedToDeleteUser')
  } finally {
    isDeleting.value = false
  }
}

const isUserBlocked = (user) => {
  if (!user) return false
  return Boolean(user.blocked_at || user.is_blocked || user.blocked)
}

const extractBlockReason = (userData) => {
  return userData?.block_reason || userData?.blockReason || userData?.blocked_reason || ''
}

const openBlockModal = async (user) => {
  blockUser.value = user
  blockError.value = null
  blockReasonInput.value = ''
  blockReasonDisplay.value = ''
  isBlocking.value = false

  try {
    const response = await api.get(`/admin/users/${user.id}`)
    const userData = response.data?.data || response.data || {}
    const blocked = Boolean(
      userData.blocked_at || userData.is_blocked || userData.blocked || isUserBlocked(user)
    )
    blockAction.value = blocked ? 'unblock' : 'block'
    blockReasonDisplay.value = extractBlockReason(userData)
  } catch (error) {
    console.error('Error loading user block info:', error)
    blockAction.value = isUserBlocked(user) ? 'unblock' : 'block'
  }

  showBlockModal.value = true
}

const closeBlockModal = () => {
  showBlockModal.value = false
  blockUser.value = null
  blockError.value = null
  blockReasonInput.value = ''
  blockReasonDisplay.value = ''
}

const confirmBlockAction = async () => {
  if (!blockUser.value) return
  isBlocking.value = true
  blockError.value = null

  try {
    if (blockAction.value === 'unblock') {
      await api.post(`/admin/users/${blockUser.value.id}/unblock`)
    } else {
      const payload = { reason: blockReasonInput.value || null }
      await api.post(`/admin/users/${blockUser.value.id}/block`, payload)
    }
    await fetchUsers(pagination.value.current_page)
    closeBlockModal()
  } catch (error) {
    console.error('Error updating block status:', error)
    blockError.value = error.response?.data?.message || t('error.unexpected')
  } finally {
    isBlocking.value = false
  }
}

watch(searchQuery, () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(() => {
    fetchUsers(1) // Reset to page 1 on new search
  }, 300) // 300ms debounce
})

const saveConfig = async () => {
  isSavingConfig.value = true
  configError.value = ''
  try {
    const payload = domains.value.map(entry => ({
        id: entry.id ?? null,
        dominio: entry.domain,
        role_id: entry.default_role_id ?? null
      }));
    await api.post('/admin/login', payload)
  } catch (error) {
    if (error.response?.data?.message) {
      configError.value = error.response.data.message
    } else {
      configError.value = t('error.failedToSaveConfig')
    }
  } finally {
    isSavingConfig.value = false
  }
}

const fetchLoginConfig = async () => {
  try {
    const response = await api.get('/admin/login')
    const items = response.data?.data || []
    domains.value = Array.isArray(items)
      ? items.map(entry => ({
          id: entry.id ?? null,
          domain: entry.dominio,
          default_role_id: entry.role?.id ?? entry.role_id ?? null
        }))
      : []
    legacyRegex.value = response.data?.legacy_regex || ''
  } catch (error) {
    console.error('Error loading login config:', error)
  }
}

let autosaveTimeout = null
watch(
  domains,
  () => {
    if (autosaveTimeout) clearTimeout(autosaveTimeout)
    autosaveTimeout = setTimeout(() => {
      saveConfig()
    }, 500)
  },
  { deep: true }
)

onMounted(() => {
  fetchGlobalData()
  fetchUsers()
  fetchLoginConfig()
})

onUnmounted(() => {
  if (searchTimeout) clearTimeout(searchTimeout)
  if (autosaveTimeout) clearTimeout(autosaveTimeout)
})
</script>