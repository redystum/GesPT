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
          <h1 class="mt-3 text-3xl font-bold">{{ $t('login.reset_title') }}</h1>
          <p class="mt-2 text-sm">{{ $t('login.reset_instructions') }}</p>
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

        <div v-if="successMessage" class="mb-6 rounded-md bg-green-50 dark:bg-green-900/30 p-4 border border-green-200 dark:border-green-800/50 transition-all">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400 dark:text-green-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800 dark:text-green-200">
                {{ successMessage }}
              </p>
            </div>
          </div>
        </div>

        <form v-if="!isSuccess" class="space-y-5" @submit.prevent="handleReset">
          <div>
            <label for="password" class="mb-1.5 block text-sm font-semibold">
              {{ $t('login.reset_password') }}
            </label>
            <input
              id="password"
              type="password"
              required
              v-model="password"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
              placeholder="••••••••••••"
            />
          </div>

          <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold">
              {{ $t('login.reset_confirm') }}
            </label>
            <input
              id="password_confirmation"
              type="password"
              required
              v-model="passwordConfirmation"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
              placeholder="••••••••••••"
            />
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300 w-full disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoading ? $t('common.loading') : $t('login.reset_submit') }}
          </button>
        </form>

        <div class="text-center mt-6">
          <router-link
            to="/login"
            class="text-sm font-medium transition-colors duration-200 hover:text-neutral-600 dark:hover:text-neutral-300"
          >
            &larr; {{ $t('login.forgot_back') }}
          </router-link>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from '@/i18n'

const route = useRoute()
const api = inject('api')
const { t } = useI18n()

const password = ref('')
const passwordConfirmation = ref('')
const errorMessage = ref('')
const successMessage = ref('')
const isLoading = ref(false)
const isSuccess = ref(false)

const token = ref('')
const email = ref('')

onMounted(() => {
  token.value = route.query.token || ''
  email.value = route.query.email || ''

  if (!token.value || !email.value) {
    errorMessage.value = t('login.reset_error')
  }
})

const handleReset = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (!token.value || !email.value) {
    errorMessage.value = t('login.reset_error')
    return
  }

  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = t('admin.add_user_password_mismatch')
    return
  }

  if (password.value.length < 8) {
    errorMessage.value = t('login.reset_error_length') || 'Password must be at least 8 characters long.'
    return
  }

  isLoading.value = true
  try {
    const response = await api.post('/reset-password', {
      email: email.value,
      token: token.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    successMessage.value = response.data.message || t('login.reset_success')
    isSuccess.value = true
    password.value = ''
    passwordConfirmation.value = ''
  } catch (error) {
    console.error('Password reset failed:', error.response ? error.response.data : error.message)
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = t('login.reset_error')
    }
  } finally {
    isLoading.value = false
  }
}
</script>
