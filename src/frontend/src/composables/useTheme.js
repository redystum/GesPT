import { ref } from 'vue'

const THEME_STORAGE_KEY = 'gespt.theme'
const darkQuery = '(prefers-color-scheme: dark)'

const isDark = ref(false)
let initialized = false
let mediaQueryList = null
let mediaQueryHandler = null

function systemPrefersDark() {
  return (
    typeof window !== 'undefined' &&
    typeof window.matchMedia === 'function' &&
    window.matchMedia(darkQuery).matches
  )
}

function applyThemeToDocument(darkMode) {
  document.documentElement.classList.toggle('dark', darkMode)
  document.documentElement.style.colorScheme = darkMode ? 'dark' : 'light'
}

function setThemeInternal(darkMode, persistPreference) {
  isDark.value = darkMode
  applyThemeToDocument(darkMode)

  if (persistPreference) {
    localStorage.setItem(THEME_STORAGE_KEY, darkMode ? 'dark' : 'light')
  }
}

function setupSystemPreferenceListener() {
  if (typeof window.matchMedia !== 'function') {
    return
  }

  mediaQueryList = window.matchMedia(darkQuery)
  mediaQueryHandler = (event) => {
    const storedTheme = localStorage.getItem(THEME_STORAGE_KEY)
    if (!storedTheme) {
      setThemeInternal(event.matches, false)
    }
  }

  mediaQueryList.addEventListener('change', mediaQueryHandler)
}

export function initializeTheme() {
  if (initialized || typeof window === 'undefined') {
    return
  }

  const storedTheme = localStorage.getItem(THEME_STORAGE_KEY)

  if (storedTheme === 'dark' || storedTheme === 'light') {
    setThemeInternal(storedTheme === 'dark', false)
  } else {
    setThemeInternal(systemPrefersDark(), false)
  }

  setupSystemPreferenceListener()
  initialized = true
}

export function useTheme() {
  if (!initialized && typeof window !== 'undefined') {
    initializeTheme()
  }

  const setTheme = (darkMode) => {
    setThemeInternal(darkMode, true)
  }

  const toggleTheme = () => {
    setTheme(!isDark.value)
  }

  return {
    isDark,
    setTheme,
    toggleTheme
  }
}
