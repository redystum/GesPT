<template>
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50 dark:bg-neutral-950 animate-fade-in-up">
    <div class="max-w-md w-full">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-neutral-900 dark:text-white mb-2 tracking-tight">
          Welcome back
        </h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
          Sign in to your Academic Curator account
        </p>
      </div>

      <div class="simple-card p-8">
        <form class="space-y-6" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Institutional Email</label>
            <input
              id="email"
              type="email"
              required
              class="simple-input"
              placeholder="you@university.edu"
            />
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Password</label>
              <a href="#" class="text-xs font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">Forgot password?</a>
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
            Sign In
          </button>
        </form>

        <div class="mt-8 text-center">
          <p class="text-[11px] text-neutral-400 dark:text-neutral-500 uppercase tracking-widest font-semibold flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            Protected by Enterprise SSO
          </p>
        </div>
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
