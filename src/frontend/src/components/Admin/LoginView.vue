<style scoped></style>

<template>
  <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-1">
      <article
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-6"
      >
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.15em]">
          {{ $t('admin.rule_title') }}
        </h2>
        <div class="mb-2 flex items-center gap-2">
          <label class="block text-xs font-semibold uppercase tracking-[0.12em]">
            {{ $t('admin.rule_pattern_label') }}
          </label>
          <div class="relative flex items-center" ref="tooltipContainer">
            <button type="button" @click="showTooltip = !showTooltip" class="leading-none flex items-center justify-center outline-none">
              <font-awesome-icon
                :icon="['fas', 'circle-question']"
                class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 cursor-pointer transition-colors text-sm"
              />
            </button>
            <div
              :class="showTooltip ? 'opacity-100 visible' : 'opacity-0 invisible'"
              class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max max-w-62.5 px-3 py-2 dark:bg-neutral-900 bg-neutral-100 dark:text-white text-neutral-900 text-sm rounded-md transition-all duration-200 pointer-events-none z-10 text-center shadow-lg font-normal tracking-normal normal-case"
            >
              {{ $t('admin.regex_helper') }}
              <a href="https://regex101.com/" target="_blank" class="underline pointer-events-auto">regex101.com</a>
              <div
                class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-neutral-100 dark:border-t-neutral-900"
              ></div>
            </div>
          </div>
        </div>
        <input
          type="text"
          v-model="regex_pattern"
          class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 mb-4 font-mono text-xs"
        />

        <div>
          <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em]">
            {{ $t('admin.test_pattern') }}
          </label>
          <textarea
            v-model="rule_behavior"
            placeholder="Enter regex to test"
            rows="5"
            class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 mb-4 font-mono text-xs"
          ></textarea>

          <ul>
            <li v-for="(isValid, index) in validationResults" :key="index">
              <span v-if="isValid" style="color: green">
                <i class="fa fa-check text-xs" />
              </span>
              <span v-else style="color: red">
                <i class="fa fa-x text-xs" />
              </span>
              {{ rule_behavior.split('\n')[index] }}
            </li>
          </ul>
        </div>

        <div v-if="!isRegexValid" class="text-red-500 text-xs mt-2">
          {{ $t('admin.invalid_regex_warning') }}
        </div>
      </article>
    </div>

    <article
      class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 overflow-hidden xl:col-span-2 flex flex-col"
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
            v-model="searchQuery"
            :placeholder="$t('admin.whitelist_search')"
            class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20 sm:w-64"
          />
          <button
            @click="showAddModal = true"
            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300 whitespace-nowrap"
          >
            <font-awesome-icon :icon="['fas', 'plus']" class="text-sm leading-none" />{{
              $t('common.add_entry')
            }}
          </button>
        </div>
      </div>

      <div class="overflow-x-auto flex-1">
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
          <tbody v-if="isLoading">
            <tr>
              <td colspan="3" class="px-5 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                {{ $t('admin.loading_users') }}
              </td>
            </tr>
          </tbody>
          <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 text-sm" v-else-if="users.length > 0">
            <tr v-for="user in users" :key="user.id" class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
              <td class="px-5 py-4">
                <div class="flex items-center gap-2">
                  <p class="font-semibold text-neutral-900 dark:text-neutral-100">{{ user.name }}</p>
                  <span v-if="user.type === 'local'" class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                    Local
                  </span>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                  {{ user.email }}
                </p>
              </td>
              <td class="px-5 py-4">
                <span
                  v-for="role in user.roles"
                  v-bind:key="role.id"
                  class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300"
                  >{{ role.nome }}
                </span>
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
          <tbody v-else>
            <tr>
              <td colspan="3" class="px-5 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                {{ $t('admin.no_users') }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        class="flex items-center justify-between border-t border-neutral-400 dark:border-neutral-600 p-4 text-xs mt-auto"
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

    <!-- Add User Modal -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
      <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-xl w-full max-w-md border border-neutral-200 dark:border-neutral-800 flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-neutral-200 dark:border-neutral-800">
          <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Add New User</h3>
          <button @click="closeAddModal" class="cursor-pointer text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300">
            <span class="text-xl leading-none">&times;</span>
          </button>
        </div>
        <div class="p-5 space-y-5">
          <div v-if="errors.general" class="p-3 bg-red-100 border border-red-200 text-red-700 rounded-md text-sm">
            {{ errors.general }}
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-[0.12em] mb-2 text-neutral-700 dark:text-neutral-300">Name</label>
            <input v-model="newUser.name" @keydown.enter="addUser" type="text" placeholder="João Pedro" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.name}" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20" />
            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-[0.12em] mb-2 text-neutral-700 dark:text-neutral-300">Email</label>
            <input v-model="newUser.email" @keydown.enter="addUser" type="email" placeholder="joao.pedro@ipleiria.pt" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.email}" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20" />
            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-[0.12em] mb-2 text-neutral-700 dark:text-neutral-300">Account Type</label>
            <select v-model="newUser.type" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.type}" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20">
              <option value="remote">Remote (SSO)</option>
              <option value="local">Local</option>
            </select>
            <p v-if="errors.type" class="text-red-500 text-xs mt-1">{{ errors.type[0] }}</p>
          </div>
          <div v-if="newUser.type === 'local'" class="space-y-5">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-[0.12em] mb-2 text-neutral-700 dark:text-neutral-300">Password</label>
              <input v-model="newUser.password" @keydown.enter="addUser" type="password" placeholder="••••••••" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500/20': errors.password}" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20" />
              <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</p>
            </div>
            <div>
              <label class="block text-xs font-semibold uppercase tracking-[0.12em] mb-2 text-neutral-700 dark:text-neutral-300">Confirm Password</label>
              <input v-model="newUser.password_confirmation" @keydown.enter="addUser" type="password" placeholder="••••••••" class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20" />
            </div>
          </div>
        </div>
        <div class="flex items-center justify-end gap-3 p-4 border-t border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50 rounded-b-lg">
          <button @click="closeAddModal" class="px-4 py-2 text-sm font-semibold transition-colors text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 cursor-pointer">Cancel</button>
          <button @click="addUser" :disabled="isAddingUser" class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
            <span v-if="isAddingUser">{{ $t('admin.saving') }}</span>
            <span v-else>{{ $t('admin.save') }}</span>
          </button>
        </div>
      </div>
    </div>
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

const fetchUsers = async () => {
  isLoading.value = true
  try {
    const url = searchQuery.value ? `/users?search=${encodeURIComponent(searchQuery.value)}` : '/users'
    const response = await api.get(url)
    users.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    isLoading.value = false
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
    fetchUsers()
  }, 300) // 300ms debounce
})

const handleClickOutside = (event) => {
  if (tooltipContainer.value && !tooltipContainer.value.contains(event.target)) {
    showTooltip.value = false
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

const validateLines = () => {
  try {
    const regex = new RegExp(regex_pattern.value)
    isRegexValid.value = true
    const lines = rule_behavior.value.split('\n')
    validationResults.value = lines.map((line) => regex.test(line))
  } catch {
    isRegexValid.value = false
    validationResults.value = []
  }
}

validateLines()

watch([rule_behavior, regex_pattern], validateLines)
</script>