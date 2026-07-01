<template>
  <div
    class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up flex flex-col justify-center min-h-[80vh]"
  >
    <div class="mx-auto max-w-md w-full">
      <section
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-7 sm:p-8"
      >
        <div class="mb-8 text-center">
          <p
            class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400"
          >
            {{ $t('login.kicker') }}
          </p>
          <h1 class="mt-3 text-3xl font-bold">{{ $t('login.welcome') }}</h1>
          <p class="mt-2 text-sm">{{ $t('login.message') }}</p>
        </div>

        <div v-if="errorMessage" class="mb-6 rounded-md bg-red-50 dark:bg-red-900/30 p-4 border border-red-200 dark:border-red-800/50 transition-all">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400 dark:text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-red-800 dark:text-red-200">
                {{ errorMessage }}
              </p>
            </div>
          </div>
        </div>

        <form v-if="!isForgotMode" class="space-y-5" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold">
              {{ $t('login.email') }}
            </label>
            <input
              id="email"
              type="email"
              required
              v-model="email"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
              placeholder="...@ipleiria.pt"
            />
          </div>

          <div>
            <div class="mb-1.5 flex items-center justify-between">
              <label for="password" class="block text-sm font-semibold">
                {{ $t('login.password') }}
              </label>
              <a href="#" @click.prevent="toggleForgotMode(true)" class="text-xs font-medium transition-colors duration-200 hover:text-neutral-600 dark:hover:text-neutral-300">{{
                $t('login.forgot')
              }}</a>
            </div>
            <input
              id="password"
              type="password"
              required
              v-model="password"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
              placeholder="••••••••••••"
            />
          </div>

          <div class="flex items-center">
            <input
              id="remember_me"
              type="checkbox"
              v-model="rememberMe"
              class="h-4 w-4 cursor-pointer rounded border-neutral-200 dark:border-neutral-700 bg-white dark:bg-[#1a1a1a] text-neutral-900 focus:ring-2 focus:ring-neutral-500/20"
            />
            <label for="remember_me" class="cursor-pointer ml-2 block text-sm">
              {{ $t('login.remember') }}
            </label>
          </div>

          <button
            type="submit"
            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300 w-full"
          >
            {{ $t('login.submit') }}
          </button>
        </form>

        <form v-else class="space-y-5" @submit.prevent="handleForgotPassword">
          <div class="mb-2">
            <h2 class="text-xl font-bold">{{ $t('login.forgot_title') }}</h2>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
              {{ $t('login.forgot_instructions') }}
            </p>
          </div>

          <div>
            <label for="forgot-email" class="mb-1.5 block text-sm font-semibold">
              {{ $t('login.email_local') }}
            </label>
            <input
              id="forgot-email"
              type="text"
              required
              v-model="forgotEmail"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
              placeholder="...@gespt.local"
            />
          </div>

          <div v-if="successMessage" class="rounded-md bg-green-50 dark:bg-green-900/30 p-4 border border-green-200 dark:border-green-800/50 transition-all">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">
              {{ successMessage }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300 w-full disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoading ? $t('common.loading') : $t('login.forgot_submit') }}
          </button>

          <div class="text-center mt-4">
            <a href="#" @click.prevent="toggleForgotMode(false)" class="text-sm font-medium transition-colors duration-200 hover:text-neutral-600 dark:hover:text-neutral-300">
              &larr; {{ $t('login.forgot_back') }}
            </a>
          </div>
        </form>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from '@/i18n'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const api = inject('api')
const { t } = useI18n()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const errorMessage = ref('')

const isForgotMode = ref(false)
const forgotEmail = ref('')
const successMessage = ref('')
const isLoading = ref(false)

const toggleForgotMode = (val) => {
  isForgotMode.value = val
  errorMessage.value = ''
  successMessage.value = ''
  forgotEmail.value = ''
}

const handleForgotPassword = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  let emailVal = forgotEmail.value.trim()
  if (emailVal && !emailVal.endsWith('@gespt.local')) {
    const username = emailVal.includes('@') ? emailVal.split('@')[0] : emailVal
    emailVal = `${username}@gespt.local`
    forgotEmail.value = emailVal
  }

  isLoading.value = true
  try {
    const response = await api.post('/forgot-password', {
      email: forgotEmail.value
    })
    successMessage.value = response.data.message || t('login.forgot_success')
  } catch (error) {
    console.error('Password recovery failed:', error.response ? error.response.data : error.message)
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = t('error.unexpected')
    }
  } finally {
    isLoading.value = false
  }
}

const handleLogin = async () => {
  errorMessage.value = ''
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
      remember: rememberMe.value
    })
    console.log('Login successful:', response.data)

    // Save the credentials and token to store
    authStore.login(response.data)

    // Fetch and store permissions
    try {
      const meResponse = await api.get('/me')
      authStore.setPermissions(meResponse.data.permissions)
    } catch (e) {
      console.error('Failed to load permissions during login:', e)
    }

    // Redirect to home or dashboard on successful login
    router.push('/')
  } catch (error) {
    console.error('Login failed:', error.response ? error.response.data : error.message)
    // Display the error message in the UI
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = t('login.error')
    }
  }
}
</script>