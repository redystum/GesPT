import '@fortawesome/fontawesome-free/css/all.min.css'
import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from '@/i18n'
import { useAuthStore } from '@/stores/auth'

import App from './App.vue'
import router from './router'
import axios from 'axios'
import en from './i18n/en.json'
import pt from './i18n/pt.json'
import { initializeTheme } from './composables/useTheme'
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { far } from '@fortawesome/free-regular-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

const LOCALE_STORAGE_KEY = 'gespt.locale'
const storedLocale = localStorage.getItem(LOCALE_STORAGE_KEY)
const browserPrefersPortuguese = navigator.language?.toLowerCase().startsWith('pt')

const initialLocale =
  storedLocale === 'pt' || storedLocale === 'en'
    ? storedLocale
    : browserPrefersPortuguese
      ? 'pt'
      : 'en'

const i18n = createI18n({
  legacy: false,
  locale: initialLocale,
  fallbackLocale: 'en',
  messages: {
    en,
    pt
  }
})

initializeTheme()

const app = createApp(App)
const pinia = createPinia()
app.use(pinia)

const API_BASE_URL = import.meta.env.VITE_API_SERVER_URL
const API_URL = import.meta.env.VITE_API_PATH_URL

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json'
  }
})

api.interceptors.request.use((config) => {
  const authStore = useAuthStore()
  config.headers = config.headers ?? {}
  config.headers['Accept-Language'] = i18n.locale.value
  
  if (authStore.token) {
    config.headers['Authorization'] = `Bearer ${authStore.token}`
  }
  
  return config
})

app.provide('apiBaseURL', API_BASE_URL)
app.provide('apiURL', API_URL)
app.provide('api', api)

app.use(router)
app.use(i18n)

library.add(fas, far, fab)

app.component('font-awesome-icon', FontAwesomeIcon)

// Refresh permissions on startup if logged in
const authStore = useAuthStore(pinia)
if (authStore.isLoggedIn) {
  api.get('/me')
    .then((response) => {
      authStore.setPermissions(response.data.permissions)
      if (response.data.user) {
        authStore.updateUser(response.data.user)
      }
    })
    .catch((error) => {
      console.error('Failed to fetch permissions on startup:', error)
      if (error.response && error.response.status === 401) {
        authStore.logout()
        router.push('/login')
      }
    })
}

app.mount('#app')