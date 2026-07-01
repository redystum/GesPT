<template>
  <div class="h-full">
    <section class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-sm flex flex-col h-[550px] sm:h-[650px] xl:h-[calc(100vh-16rem)] min-h-[400px] w-full">
      <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 border-b border-neutral-100 dark:border-neutral-800 shrink-0">
        <div>
          <h2 class="text-base font-bold text-neutral-900 dark:text-neutral-100">{{ $t('admin.roles_title') || 'Roles' }}</h2>
          <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $t('admin.roles_subtitle') || 'Manage user roles and permissions' }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
          <div class="relative">
            <font-awesome-icon :icon="['fas', 'search']" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm" />
            <input
              type="text"
              v-model="searchQuery"
              :placeholder="$t('admin.roles_search') || 'Search roles...'"
              class="w-full sm:w-64 rounded-lg border border-neutral-300 dark:border-neutral-700 pl-9 pr-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow"
            />
          </div>
          <button
            v-if="authStore.hasPermission('roles.create')"
            @click="openCreateModal"
            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-200 shadow-sm whitespace-nowrap"
          >
            <font-awesome-icon :icon="['fas', 'plus']" class="text-sm" />
            {{ $t('admin.add_role') || 'Add Role' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-x-auto overflow-y-auto">
        <table class="w-full text-left whitespace-nowrap text-sm">
          <thead class="sticky top-0 z-10 bg-neutral-50 dark:bg-[#1a1a1a] backdrop-blur-sm border-b border-neutral-200 dark:border-neutral-800">
            <tr class="text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
              <th class="px-6 py-4">{{ $t('admin.table_role_name') || 'Role Name' }}</th>
              <th class="px-6 py-4">{{ $t('admin.table_role_desc') || 'Description' }}</th>
              <th class="px-6 py-4">{{ $t('admin.table_permissions_count') || 'Permissions' }}</th>
              <th class="px-6 py-4">{{ $t('admin.table_users_count') || 'Users' }}</th>
              <th v-if="authStore.hasPermission(['roles.update', 'roles.delete'])" class="px-6 py-4 text-right">{{ $t('admin.table_actions') || 'Actions' }}</th>
            </tr>
          </thead>
          <tbody v-if="isLoading" class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <tr>
              <td colspan="5" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                <font-awesome-icon :icon="['fas', 'spinner']" spin class="mr-2 text-neutral-400" />
                {{ $t('admin.loading_roles') || 'Loading roles...' }}
              </td>
            </tr>
          </tbody>
          <tbody v-else-if="errorMessage" class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <tr>
              <td colspan="5" class="px-6 py-12 text-center text-red-500 dark:text-red-400 bg-red-50/50 dark:bg-red-900/10">
                <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="mr-2" />
                {{ errorMessage }}
              </td>
            </tr>
          </tbody>
          <tbody v-else-if="roles.length > 0" class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <tr v-for="role in roles" :key="role.id" class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/30 group">
              <td class="px-6 py-3.5">
                <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ role.nome || role.name }}</p>
              </td>
              <td class="px-6 py-3.5">
                <p class="text-neutral-500 dark:text-neutral-400 truncate max-w-[200px] lg:max-w-[300px]" :title="role.descricao || role.description">{{ role.descricao || role.description || '-' }}</p>
              </td>
              <td class="px-6 py-3.5">
                <span class="text-neutral-700 dark:text-neutral-300 font-medium">
                  {{ role.permissoes ?? role.permissions_count ?? 0 }}
                </span>
              </td>
              <td class="px-6 py-3.5">
                <span class="text-neutral-700 dark:text-neutral-300 font-medium">
                  {{ role.users ?? role.users_count ?? 0 }}
                </span>
              </td>
              <td v-if="authStore.hasPermission(['roles.update', 'roles.delete'])" class="px-6 py-3.5 text-right">
                  <button
                    v-if="authStore.hasPermission('roles.update')"
                    @click="editRole(role)"
                    :disabled="fetchingRoleId === role.id || role.id === 1"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-md text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                    :title="$t('admin.edit') || 'Edit'"
                  >
                    <font-awesome-icon v-if="fetchingRoleId === role.id" :icon="['fas', 'spinner']" spin class="text-sm" />
                    <font-awesome-icon v-else :icon="['fas', 'pen']" class="text-sm" />
                  </button>
                  <button
                    v-if="authStore.hasPermission('roles.delete')"
                    @click="confirmDelete(role)"
                    :disabled="role.id === 1"
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
              <td colspan="5" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                {{ $t('admin.no_roles') || 'No roles found.' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <footer class="flex items-center justify-between border-t border-neutral-200 dark:border-neutral-800 px-6 py-4 bg-neutral-50/50 dark:bg-neutral-800/10 shrink-0">
        <span class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">
          {{ pagination.from || 0 }} - {{ pagination.to || 0 }} {{ $t('admin.of') || 'of' }} {{ pagination.total || 0 }} {{ $t('admin.total_roles') || 'total roles' }}
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

    <!-- Role Modal -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showRoleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="closeRoleModal"></div>

        <!-- Modal Content -->
        <div class="relative w-full max-w-5xl max-h-[85vh] h-[750px] flex flex-col bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl overflow-hidden border border-neutral-200 dark:border-neutral-800">
          
          <!-- Header -->
          <header class="flex items-center justify-between p-6 border-b border-neutral-100 dark:border-neutral-800 shrink-0">
            <div>
              <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                {{ editingRole ? $t('admin.edit_role') : $t('admin.add_role') }}
              </h3>
              <p class="text-sm text-neutral-500 mt-1">{{ editingRole ? $t('admin.edit_role_desc') : $t('admin.add_role_desc') }}</p>
            </div>
            <button @click="closeRoleModal" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 transition-colors p-2 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer">
              <font-awesome-icon :icon="['fas', 'times']" />
            </button>
          </header>
          
          <!-- Body -->
          <div class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-neutral-50/30 dark:bg-[#1a1a1a]">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              
              <!-- Left Column: Role Details -->
              <div class="lg:col-span-1 lg:border-r border-neutral-100 dark:border-neutral-800 lg:pr-6">
                 <div class="lg:sticky lg:top-0 space-y-6">
                   <div>
                     <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">{{ $t('admin.role_name_label') || 'Role Name' }}</label>
                     <input v-model="roleForm.name" type="text" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow" :placeholder="$t('admin.role_name_placeholder') || 'e.g. Content Editor'" />
                   </div>
                   <div>
                     <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">{{ $t('admin.role_desc_label') || 'Description' }}</label>
                     <textarea v-model="roleForm.description" rows="4" class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow resize-none" :placeholder="$t('admin.role_desc_placeholder') || 'Brief description...'"></textarea>
                   </div>
                 </div>
              </div>

              <!-- Right Column: Permissions Categories -->
              <div class="lg:col-span-2 space-y-8">
                <div v-for="category in permissionCategories" :key="category.id || category.nome">
                  <h4 class="text-sm font-bold uppercase tracking-wider text-neutral-800 dark:text-neutral-200 mb-4 border-b border-neutral-200 dark:border-neutral-800 pb-2">{{ category.nome || category.name }}</h4>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label v-for="perm in category.permissoes" :key="perm.id" class="flex items-start gap-3 cursor-pointer group p-3 rounded-lg border border-neutral-100 dark:border-neutral-800 hover:border-neutral-300 dark:hover:border-neutral-600 transition-colors bg-white dark:bg-[#1a1a1a] hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                      <div class="relative flex items-center justify-center mt-0.5">
                        <input
                          type="checkbox"
                          :checked="roleForm.permissions.includes(perm.id)"
                          @change="togglePermission(perm)"
                          class="peer sr-only"
                        />
                        <div class="w-5 h-5 rounded border-2 border-neutral-300 dark:border-neutral-600 peer-checked:bg-neutral-900 peer-checked:border-neutral-900 dark:peer-checked:bg-neutral-100 dark:peer-checked:border-neutral-100 peer-focus-visible:ring-2 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-neutral-900 transition-colors flex items-center justify-center peer-checked:[&>svg]:opacity-100">
                          <svg class="text-white dark:text-neutral-900 w-3 h-3 opacity-0 transition-opacity" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <span class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-neutral-100 transition-colors">{{ perm.nome || perm.name }}</span>
                        <p class="text-xs text-neutral-500 mt-1 leading-relaxed">{{ perm.descricao || perm.description }}</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Error Banner -->
          <div v-if="saveError" class="px-6 py-3 bg-red-50 dark:bg-red-900/10 border-t border-red-100 dark:border-red-900/20 text-red-600 dark:text-red-400 text-sm flex items-center gap-2 shrink-0">
            <font-awesome-icon :icon="['fas', 'triangle-exclamation']" />
            {{ saveError }}
          </div>

          <!-- Footer -->
          <footer class="p-4 sm:p-6 border-t border-neutral-100 dark:border-neutral-800 flex justify-end gap-3 bg-neutral-50/80 dark:bg-neutral-900/30 shrink-0">
            <button @click="closeRoleModal" class="px-4 py-2 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors cursor-pointer">
              {{ $t('common.cancel') || 'Cancel' }}
            </button>
            <button @click="saveRole" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg px-6 py-2 text-sm font-medium transition-colors bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-200 shadow-sm" :disabled="isSaving">
              <font-awesome-icon v-if="isSaving" :icon="['fas', 'spinner']" spin class="text-sm" />
              {{ $t('admin.save_role') || 'Save Role' }}
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
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="closeDeleteModal"></div>
        <div class="relative w-full max-w-sm bg-white dark:bg-[#1a1a1a] rounded-2xl shadow-xl overflow-hidden border border-neutral-200 dark:border-neutral-800 p-6">
          <div class="flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4 text-red-600 dark:text-red-400">
              <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="text-xl" />
            </div>
            <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-2">{{ $t('admin.delete_role_title') || 'Delete Role' }}</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6">
              {{ $t('admin.delete_role_desc') || 'Are you sure you want to delete this role? This action cannot be undone.' }}
              <br/><br/>
              <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ roleToDelete?.nome || roleToDelete?.name }}</span>
            </p>
            
            <div v-if="deleteError" class="w-full mb-6 px-4 py-3 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/20 rounded-lg text-red-600 dark:text-red-400 text-sm flex items-start text-left gap-2">
              <font-awesome-icon :icon="['fas', 'circle-exclamation']" class="mt-0.5 shrink-0" />
              <span>{{ deleteError }}</span>
            </div>

            <div class="flex gap-3 w-full">
              <button @click="closeDeleteModal" class="flex-1 px-4 py-2 text-sm font-medium rounded-lg text-neutral-600 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors cursor-pointer">
                {{ $t('common.cancel') || 'Cancel' }}
              </button>
              <button @click="deleteRole" class="flex-1 inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors bg-red-600 text-white hover:bg-red-700 shadow-sm" :disabled="isDeleting">
                <font-awesome-icon v-if="isDeleting" :icon="['fas', 'spinner']" spin />
                {{ $t('admin.delete_role_confirm') || 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, inject, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'

const api = inject('api')
const authStore = useAuthStore()

const roles = ref([])
const searchQuery = ref('')
const isLoading = ref(true)
const errorMessage = ref(null)
let searchTimeout = null

const showRoleModal = ref(false)
const editingRole = ref(false)
const isSaving = ref(false)
const fetchingRoleId = ref(null)
const saveError = ref(null)

const showDeleteModal = ref(false)
const roleToDelete = ref(null)
const isDeleting = ref(false)
const deleteError = ref(null)

const roleForm = ref({
  id: null,
  name: '',
  description: '',
  permissions: []
})

const permissionCategories = ref([])

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

const fetchRoles = async (page = 1) => {
  isLoading.value = true
  errorMessage.value = null
  try {
    let url = `admin/roles?page=${page}`
    if (searchQuery.value) {
      url += `&search=${encodeURIComponent(searchQuery.value)}`
    }
    const response = await api.get(url)
    roles.value = response.data.data || []

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
        pagination.value = {
            current_page: response.data.current_page || 1,
            last_page: response.data.last_page || 1,
            per_page: response.data.per_page || 15,
            total: response.data.total || roles.value.length,
            from: response.data.from || (roles.value.length ? 1 : 0),
            to: response.data.to || roles.value.length,
            prev_page_url: response.data.prev_page_url,
            next_page_url: response.data.next_page_url,
        }
    }
  } catch (error) {
    console.error('Error fetching roles:', error)
    errorMessage.value = error.response?.data?.message || 'Failed to load roles. Please try again later.'
    roles.value = []
    pagination.value.total = 0
  } finally {
    isLoading.value = false
  }
}

const fetchPermissions = async () => {
  try {
    const response = await api.get('admin/permissions')
    permissionCategories.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Error fetching permissions:', error)
  }
}

const findPermissionByCode = (code) => {
  for (const category of permissionCategories.value) {
    if (category.permissoes) {
      const found = category.permissoes.find(p => p.permissao === code)
      if (found) return found
    }
  }
  return null
}

const activatePermissionRecursive = (perm, activeSet) => {
  if (activeSet.has(perm.id)) return
  activeSet.add(perm.id)

  if (perm.required_permissoes && perm.required_permissoes.length > 0) {
    perm.required_permissoes.forEach(reqPermCode => {
      const reqPerm = findPermissionByCode(reqPermCode)
      if (reqPerm) {
        activatePermissionRecursive(reqPerm, activeSet)
      }
    })
  }
}

const deactivatePermissionRecursive = (permCode, activeSet) => {
  const perm = findPermissionByCode(permCode)
  if (!perm) return

  if (!activeSet.has(perm.id)) return
  activeSet.delete(perm.id)

  permissionCategories.value.forEach(category => {
    if (category.permissoes) {
      category.permissoes.forEach(p => {
        if (p.required_permissoes && p.required_permissoes.includes(permCode)) {
          deactivatePermissionRecursive(p.permissao, activeSet)
        }
      })
    }
  })
}

const togglePermission = (perm) => {
  const activeSet = new Set(roleForm.value.permissions)
  if (activeSet.has(perm.id)) {
    if (perm.permissao) {
      deactivatePermissionRecursive(perm.permissao, activeSet)
    } else {
      activeSet.delete(perm.id)
    }
  } else {
    activatePermissionRecursive(perm, activeSet)
  }
  roleForm.value.permissions = Array.from(activeSet)
}

const fetchPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchRoles(page)
  }
}

const openCreateModal = () => {
  editingRole.value = false
  saveError.value = null
  roleForm.value = {
    id: null,
    name: '',
    description: '',
    permissions: []
  }
  showRoleModal.value = true
}

const editRole = async (role) => {
  fetchingRoleId.value = role.id
  saveError.value = null
  try {
    const response = await api.get(`admin/roles/${role.id}`)
    const roleData = response.data.data || response.data
    
    editingRole.value = true
    roleForm.value = {
      id: roleData.id,
      name: roleData.nome || roleData.name,
      description: roleData.descricao || roleData.description || '',
      permissions: (roleData.permissoes || roleData.permissions || []).map(p => p.id)
    }
    showRoleModal.value = true
  } catch (error) {
    console.error('Error fetching role details:', error)
  } finally {
    fetchingRoleId.value = null
  }
}

const closeRoleModal = () => {
  showRoleModal.value = false
}

const saveRole = async () => {
  isSaving.value = true
  saveError.value = null
  try {
    const payload = {
      nome: roleForm.value.name,
      descricao: roleForm.value.description,
      permissoes: roleForm.value.permissions
    }

    if (editingRole.value) {
      //payload.id = roleForm.value.id
      await api.put(`admin/roles/${roleForm.value.id}`, payload)
    } else {
      await api.post('admin/roles', payload)
    }
    
    await fetchRoles(pagination.value.current_page)
    closeRoleModal()
  } catch (error) {
    console.error('Error saving role:', error)
    saveError.value = error.response?.data?.message || error.response?.data?.error || 'Failed to save role. Please check your inputs and try again.'
  } finally {
    isSaving.value = false
  }
}

const confirmDelete = (role) => {
  roleToDelete.value = role
  deleteError.value = null
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  roleToDelete.value = null
  deleteError.value = null
}

const deleteRole = async () => {
  if (!roleToDelete.value) return
  isDeleting.value = true
  deleteError.value = null
  try {
    await api.delete(`admin/roles/${roleToDelete.value.id}`)
    
    // Refresh table and close modal
    await fetchRoles(pagination.value.current_page)
    closeDeleteModal()
  } catch (error) {
    console.error('Error deleting role:', error)
    deleteError.value = error.response?.data?.message || error.response?.data?.error || 'Failed to delete role. It might be in use.'
  } finally {
    isDeleting.value = false
  }
}

watch(searchQuery, () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(() => {
    fetchRoles(1)
  }, 300)
})

onMounted(() => {
  fetchRoles()
  fetchPermissions()
})

onUnmounted(() => {
  if (searchTimeout) clearTimeout(searchTimeout)
})
</script>
