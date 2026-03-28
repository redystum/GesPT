<template>
  <div
    class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50 dark:bg-neutral-950 animate-fade-in-up"
  >
    <div class="max-w-md w-full">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-neutral-900 dark:text-white mb-2 tracking-tight">
          {{ $t('login.welcome') }}
        </h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
          {{ $t('login.message') }}
        </p>
      </div>

      <div class="simple-card p-8">
        <form class="space-y-6" @submit.prevent="handleLogin">
          <div>
            <label
              for="email"
              class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2"
              >{{ $t('login.email') }}</label
            >
            <input
              id="email"
              type="email"
              required
              class="simple-input"
              placeholder="...@ipleiria.pt"
            />
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <label
                for="password"
                class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >{{ $t('login.password') }}</label
              >
              <a
                href="#"
                class="text-xs font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors"
                >{{ $t('login.forgot') }}</a
              >
            </div>
            <input
              id="password"
              type="password"
              required
              class="simple-input"
              placeholder="••••••••••••"
            />
          </div>

          <button type="submit" class="simple-button w-full py-2.5 mt-2">
            {{ $t('login.submit') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const handleLogin = () => {
  // In a real app, handle authentication
  router.push('/')
}
</script>
