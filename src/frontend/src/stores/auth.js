import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null)
  const token = ref(localStorage.getItem('token') || null)

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
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  }

  return { user, token, isLoggedIn, initials, login, logout }
})