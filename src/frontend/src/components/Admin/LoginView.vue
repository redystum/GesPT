<style scoped></style>

<template>
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8 items-start">
    <!-- Rule Configuration -->
    <div class="xl:col-span-1 space-y-6">
      <section class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-sm flex flex-col p-5 sm:p-6">
        <header class="mb-5">
          <h2 class="text-sm font-bold uppercase tracking-wider text-neutral-900 dark:text-neutral-100">
            {{ $t('admin.rule_title') }}
          </h2>
        </header>

        <div class="space-y-5 flex-1">
          <div>
            <div class="mb-2 flex items-center gap-2">
              <label class="text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
                {{ $t('admin.rule_pattern_label') }}
              </label>
              <div class="relative flex items-center" ref="tooltipContainer">
                <button type="button" @click="showTooltip = !showTooltip" class="outline-none flex">
                  <font-awesome-icon :icon="['fas', 'circle-question']" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 transition-colors text-sm" />
                </button>
                <!-- Tooltip -->
                <div
                  :class="showTooltip ? 'opacity-100 visible translate-y-0' : 'opacity-0 invisible translate-y-1'"
                  class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max max-w-[200px] sm:max-w-xs px-3 py-2 bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 text-xs rounded shadow-lg transition-all duration-200 z-10 text-center pointer-events-auto"
                >
                  {{ $t('admin.regex_helper') }}
                  <a href="https://regex101.com/" target="_blank" class="underline font-medium ml-1">regex101.com</a>
                  <div class="absolute top-full left-1/2 -translate-x-1/2 border-[5px] border-transparent border-t-neutral-900 dark:border-t-white"></div>
                </div>
              </div>
            </div>
            <input
              type="text"
              v-model="regex_pattern"
              class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow font-mono"
            />
            <div v-if="backendRegex" class="mt-2 text-[11px] text-neutral-500 dark:text-neutral-400 font-mono break-all bg-neutral-100 dark:bg-neutral-800/50 p-2 rounded">
              <p class="font-semibold">{{ $t('admin.final_regex') }}:</p> <p>{{ backendRegex }} </p>
            </div>
          </div>

          <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400">
              {{ $t('admin.test_pattern') }}
            </label>
            <textarea
              v-model="rule_behavior"
              placeholder="Enter items to test (one per line)"
              rows="4"
              class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow font-mono resize-y min-h-[100px]"
            ></textarea>
          </div>
          
          <div v-if="validationResults.length > 0 && rule_behavior" class="space-y-1.5">
            <div 
              v-for="(line, index) in rule_behavior.split('\n').filter(l => l.trim() !== '')" 
              :key="index"
              class="flex items-center gap-2 text-xs font-mono py-1.5 px-3 rounded-md"
              :class="validationResults[index] ? 'bg-green-50 dark:bg-green-900/10 text-green-700 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/10 text-red-700 dark:text-red-400'"
            >
              <font-awesome-icon :icon="validationResults[index] ? ['fas', 'check'] : ['fas', 'xmark']" class="shrink-0" />
              <span class="truncate">{{ line }}</span>
            </div>
          </div>

          <div v-if="!isRegexValid" class="text-red-600 dark:text-red-400 text-xs font-medium bg-red-50 dark:bg-red-900/10 p-3 rounded-md flex items-start gap-2">
            <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="mt-0.5" />
            <span>{{ regexError || $t('admin.invalid_regex_warning') }}</span>
          </div>
        </div>

        <footer class="mt-8 pt-5 border-t border-neutral-100 dark:border-neutral-800 flex flex-wrap items-center gap-3">
          <button
            class="flex-1 sm:flex-none cursor-pointer items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-colors border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-[#1a1a1a] text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-neutral-900/10 dark:focus:ring-neutral-100/10"
            @click="discardChanges"
            :disabled="isDiscarding"
          >
            <span v-if="isDiscarding">{{ $t('common.loading') || 'Loading...' }}</span>
            <span v-else>{{ $t('common.discard') }}</span>
          </button>
          <button
            class="flex-1 sm:flex-none cursor-pointer items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-colors bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-900/20 dark:focus:ring-neutral-100/20"
            @click="saveRegex"
            :disabled="isSavingRegex"
          >
            <span v-if="isSavingRegex">{{ $t('admin.saving') || 'Saving...' }}</span>
            <span v-else>{{ $t('admin.save_configuration') }}</span>
          </button>
        </footer>
      </section>
    </div>

    <!-- Whitelist Table -->
    <section class="xl:col-span-2 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-sm flex flex-col h-[550px] sm:h-[650px]">
      <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 border-b border-neutral-100 dark:border-neutral-800 shrink-0">
        <div>
          <h2 class="text-base font-bold text-neutral-900 dark:text-neutral-100">{{ $t('admin.whitelist_title') }}</h2>
          <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $t('admin.whitelist_subtitle') }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
          <div class="relative">
            <font-awesome-icon :icon="['fas', 'search']" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm" />
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
          <thead class="sticky top-0 z-10 bg-neutral-50 dark:bg-[#1a1a1a] backdrop-blur-sm border-b border-neutral-200 dark:border-neutral-800">
            <tr class="text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
              <th class="px-6 py-4">{{ $t('admin.table_identifier') }}</th>
              <th class="px-6 py-4">{{ $t('admin.table_role') }}</th>
              <th class="px-6 py-4 text-right">{{ $t('admin.table_actions') }}</th>
            </tr>
          </thead>
          <tbody v-if="isLoading" class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <tr>
              <td colspan="3" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                <font-awesome-icon :icon="['fas', 'spinner']" spin class="mr-2 text-neutral-400" />
                {{ $t('admin.loading_users') }}
              </td>
            </tr>
          </tbody>
          <tbody v-else-if="users.length > 0" class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <tr v-for="user in users" :key="user.id" class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/30 group">
              <td class="px-6 py-3.5">
                <div class="flex items-center gap-2.5">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <p class="font-medium text-neutral-900 dark:text-neutral-100 truncate">{{ user.name }}</p>
                      <span v-if="user.type === 'local'" class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 tracking-wide uppercase">
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
              <td class="px-6 py-3.5 text-right">
                <button
                  class="inline-flex items-center justify-center w-8 h-8 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                >
                  <font-awesome-icon :icon="['fas', 'ellipsis-vertical']" />
                </button>
              </td>
            </tr>
          </tbody>
          <tbody v-else class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <tr>
              <td colspan="3" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                {{ $t('admin.no_users') }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <footer class="flex items-center justify-between border-t border-neutral-200 dark:border-neutral-800 px-6 py-4 bg-neutral-50/50 dark:bg-neutral-800/10 shrink-0">
        <span class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">
          {{ pagination.from || 0 }} - {{ pagination.to || 0 }} {{ $t('admin.of') }} {{ pagination.total || 0 }} {{ $t('admin.total_users') }}
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
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm transition-opacity" @click="closeAddModal"></div>
        <div class="relative bg-white dark:bg-[#1a1a1a] rounded-xl shadow-2xl w-full max-w-md border border-neutral-200 dark:border-neutral-800 flex flex-col overflow-hidden">
          <header class="flex items-center justify-between px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/20">
            <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">{{ $t('admin.add_user_title') }}</h3>
            <button @click="closeAddModal" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 transition-colors outline-none rounded-md p-1 cursor-pointer">
              <font-awesome-icon :icon="['fas', 'xmark']" />
            </button>
          </header>
          
          <div class="p-6 space-y-5 overflow-y-auto max-h-[calc(100vh-16rem)]">
            <div v-if="errors.general" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/50 text-red-700 dark:text-red-400 rounded-lg text-sm font-medium flex items-start gap-2">
              <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="mt-0.5" />
              <span>{{ errors.general }}</span>
            </div>
            
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300">{{ $t('admin.add_user_name') }}</label>
                <input v-model="newUser.name" @keydown.enter="addUser" type="text" placeholder="João Pedro" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.name}" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100" />
                <p v-if="errors.name" class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium">{{ errors.name[0] }}</p>
              </div>
              
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300">{{ $t('admin.add_user_email') }}</label>
                <input v-model="newUser.email" @keydown.enter="addUser" type="email" placeholder="joao.pedro@ipleiria.pt" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.email}" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100" />
                <p v-if="errors.email" class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium">{{ errors.email[0] }}</p>
              </div>
              
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300">{{ $t('admin.add_user_type') }}</label>
                <div class="relative">
                  <select v-model="newUser.type" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.type}" class="w-full appearance-none rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 cursor-pointer">
                    <option value="remote">{{ $t('admin.add_user_type_remote') }}</option>
                    <option value="local">{{ $t('admin.add_user_type_local') }}</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-500">
                    <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-xs" />
                  </div>
                </div>
                <p v-if="errors.type" class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium">{{ errors.type[0] }}</p>
              </div>
              
              <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
              >
                <div v-if="newUser.type === 'local'" class="space-y-4 pt-2 border-t border-neutral-100 dark:border-neutral-800">
                  <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300">{{ $t('admin.add_user_password') }}</label>
                    <input v-model="newUser.password" @keydown.enter="addUser" type="password" placeholder="••••••••" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.password}" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100" />
                    <p v-if="errors.password" class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium">{{ errors.password[0] }}</p>
                  </div>
                  <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-neutral-700 dark:text-neutral-300">{{ $t('admin.add_user_password_confirm') }}</label>
                    <input v-model="newUser.password_confirmation" @keydown.enter="addUser" type="password" placeholder="••••••••" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3.5 py-2.5 text-sm outline-none transition-shadow bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100" />
                  </div>
                </div>
              </Transition>
            </div>
          </div>
          
          <footer class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 px-6 py-4 border-t border-neutral-100 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/20">
            <button @click="closeAddModal" class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium transition-colors text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 bg-transparent hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg cursor-pointer">
              {{ $t('common.cancel') }}
            </button>
            <button @click="addUser" :disabled="isAddingUser" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg px-6 py-2.5 text-sm font-medium transition-colors bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-neutral-900/20 dark:focus:ring-neutral-100/20 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm cursor-pointer">
              <span v-if="isAddingUser">{{ $t('admin.saving') }}</span>
              <span v-else>{{ $t('admin.save') }}</span>
            </button>
          </footer>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, inject } from 'vue'

const api = inject('api')

const rule_behavior = ref(
  '2231034@my.ipleiria.pt\n' + '2231034@ipleiria.pt\n' + 'ruben.alves@ipleiria.pt'
)
const validationResults = ref([])
const regex_pattern = ref('^[a-zA-Z0-9._%+-]+@ipleiria.pt$')
const isRegexValid = ref(true)
const regexError = ref('')
const backendRegex = ref('')

const isSavingRegex = ref(false)
const isDiscarding = ref(false)

const showTooltip = ref(false)
const tooltipContainer = ref(null)

const users = ref([])
const searchQuery = ref('')
const isLoading = ref(true)
let searchTimeout = null

const showAddModal = ref(false)
const isAddingUser = ref(false)
const newUser = ref({ name: '', email: '', type: 'remote', password: '', password_confirmation: '' })
const errors = ref({})

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
    let url = `/users?page=${page}`
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
  newUser.value = { name: '', email: '', type: 'remote', password: '', password_confirmation: '' }
  errors.value = {}
}

const addUser = async () => {
  isAddingUser.value = true
  errors.value = {}
  try {
    const payload = { ...newUser.value }
    if (payload.type === 'remote') {
      delete payload.password
      delete payload.password_confirmation
    }

    await api.post('/users', payload)
    closeAddModal()
    fetchUsers()
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      if (error.response.data.message && !error.response.data.errors) {
         errors.value = { general: error.response.data.message }
      }
    } else {
      errors.value = { general: 'An unexpected error occurred. Please try again.' }
      console.error('Error adding user:', error)
    }
  } finally {
    isAddingUser.value = false
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

const handleClickOutside = (event) => {
  if (tooltipContainer.value && !tooltipContainer.value.contains(event.target)) {
    showTooltip.value = false
  }
}

let validateTimeout = null
const validateLines = () => {
  if (validateTimeout) clearTimeout(validateTimeout)
  validateTimeout = setTimeout(async () => {
    isRegexValid.value = true
    regexError.value = ''
    try {
      const allLines = rule_behavior.value.split('\n')
      const lines = allLines.filter(line => line.trim() !== '')
      if (!regex_pattern.value) {
        validationResults.value = []
        backendRegex.value = ''
        return
      }
      if (lines.length === 0) {
        validationResults.value = allLines.map(() => false)
        return
      }

      const response = await api.post('/admin/regex/test', {
        regex: regex_pattern.value,
        test_subjects: lines
      })

      backendRegex.value = response.data.regex

      const resultMap = Object.fromEntries(
        (response.data.results || []).map(r => [r.subject, r.valid])
      )

      validationResults.value = allLines.map(line => {
        if (line.trim() === '') return false
        return !!resultMap[line]
      })
    } catch (error) {
      isRegexValid.value = false
      validationResults.value = []
      backendRegex.value = ''

      if (error.response?.data?.errors?.regex) {
        regexError.value = error.response.data.errors.regex[0]
      } else if (error.response?.data?.message) {
        regexError.value = error.response.data.message
      }
    }
  }, 300)
}

const saveRegex = async () => {
  isSavingRegex.value = true
  regexError.value = ''
  try {
    const lines = rule_behavior.value.split('\n').filter(line => line.trim() !== '')
    await api.post('/admin/regex/update', {
      regex: regex_pattern.value,
      test_subjects: lines
    })
    // Optional: show a success message or toast here
  } catch (error) {
    if (error.response?.data?.errors?.regex) {
      regexError.value = error.response.data.errors.regex[0]
    } else if (error.response?.data?.message) {
      regexError.value = error.response.data.message
    } else {
      regexError.value = 'Failed to save configuration.'
    }
  } finally {
    isSavingRegex.value = false
  }
}

const discardChanges = async () => {
  isDiscarding.value = true
  regexError.value = ''
  try {
    const response = await api.get('/admin/regex/get')
    if (response.data.regex !== undefined) {
      regex_pattern.value = response.data.regex
    }
    if (response.data.test_subjects !== undefined) {
      if (Array.isArray(response.data.test_subjects)) {
        rule_behavior.value = response.data.test_subjects.join('\n')
      } else {
        rule_behavior.value = response.data.test_subjects
      }
    }
  } catch (error) {
    if (error.response?.data?.message) {
      regexError.value = error.response.data.message
    } else {
      regexError.value = 'Failed to discard changes.'
    }
  } finally {
    isDiscarding.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  fetchUsers()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  if (searchTimeout) clearTimeout(searchTimeout)
})


validateLines()

watch([rule_behavior, regex_pattern], validateLines)
</script>