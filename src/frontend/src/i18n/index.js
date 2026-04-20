import { inject, ref } from 'vue'

const I18N_KEY = Symbol('gespt-i18n')

function getValueByPath(source, path) {
  return path.split('.').reduce((acc, segment) => {
    if (acc && typeof acc === 'object' && segment in acc) {
      return acc[segment]
    }

    return undefined
  }, source)
}

function createTranslator(messages, localeRef, fallbackLocale) {
  return (path) => {
    const localeMessages = messages[localeRef.value] ?? {}
    const fallbackMessages = messages[fallbackLocale] ?? {}

    const translated =
      getValueByPath(localeMessages, path) ?? getValueByPath(fallbackMessages, path) ?? path

    return typeof translated === 'string' ? translated : path
  }
}

export function createI18n(options) {
  const locale = ref(options.locale ?? 'en')
  const fallbackLocale = options.fallbackLocale ?? 'en'
  const messages = options.messages ?? {}
  const t = createTranslator(messages, locale, fallbackLocale)

  const i18nContext = {
    locale,
    t
  }

  return {
    locale,
    t,
    install(app) {
      app.provide(I18N_KEY, i18nContext)
      app.config.globalProperties.$t = t
    }
  }
}

export function useI18n() {
  const context = inject(I18N_KEY)

  if (!context) {
    throw new Error('i18n context was not found. Ensure app.use(i18n) is called before mount.')
  }

  return context
}
