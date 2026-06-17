import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null)
  const token = ref(localStorage.getItem('token') || null)
  const permissions = ref(
    localStorage.getItem('permissions') ? JSON.parse(localStorage.getItem('permissions')) : []
  )

  const isLoggedIn = computed(() => !!user.value && !!token.value)
  const initials = computed(() => {
    if (user.value && user.value.name) {
      return user.value.name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
    }
    return ''
  })

  function login(data) {
    user.value = data.user
    token.value = data.access_token
    localStorage.setItem('user', JSON.stringify(data.user))
    localStorage.setItem('token', data.access_token)
  }

  function logout() {
    user.value = null
    token.value = null
    permissions.value = []
    localStorage.removeItem('user')
    localStorage.removeItem('token')
    localStorage.removeItem('permissions')
  }

  function setPermissions(perms) {
    const permSlugs = Array.isArray(perms) ? perms.map((p) => p.permissao) : []
    permissions.value = permSlugs
    localStorage.setItem('permissions', JSON.stringify(permSlugs))
  }

  function hasPermission(permissionName) {
    if (!isLoggedIn.value) return false
    if (Array.isArray(permissionName)) {
      return permissionName.some((p) => permissions.value.includes(p))
    }
    return permissions.value.includes(permissionName)
  }

  return { user, token, permissions, isLoggedIn, initials, login, logout, setPermissions, hasPermission }
})