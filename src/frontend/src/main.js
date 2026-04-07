import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from '@/i18n'

import App from './App.vue'
import router from './router'
import en from './i18n/en.json'
import pt from './i18n/pt.json'
import { initializeTheme } from './composables/useTheme'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faSearch, faPlus, faSun, faMoon, faKeyboard, faEllipsisVertical, faExclamationCircle, faInfoCircle, faTimes, faCheck, faCircle, faCircleNotch, faChevronLeft, faChevronRight, faArrowRight } from '@fortawesome/free-solid-svg-icons'

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

const API_BASE_URL = import.meta.env.VITE_API_SERVER_URL
const API_URL = import.meta.env.VITE_API_PATH_URL

app.provide('apiBaseURL', API_BASE_URL)
app.provide('apiURL', API_URL)

app.use(createPinia())
app.use(router)
app.use(i18n)

library.add(faSearch, faPlus, faSun, faMoon, faKeyboard, faEllipsisVertical, faExclamationCircle, faInfoCircle, faTimes, faCheck, faCircle, faCircleNotch, faChevronLeft, faChevronRight, faArrowRight)

app.component('font-awesome-icon', FontAwesomeIcon)

app.mount('#app')
