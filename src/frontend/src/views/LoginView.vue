<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up">
    <div class="mx-auto max-w-md">
      <section class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-7 sm:p-8">
        <div class="mb-8 text-center">
          <p class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400">{{ $t('login.kicker') }}</p>
          <h1 class="mt-3 text-3xl font-bold">{{ $t('login.welcome') }}</h1>
          <p class="mt-2 text-sm text-[color:var(--muted)]">{{ $t('login.message') }}</p>
        </div>

        <form class="space-y-5" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-[color:var(--text)]">
              {{ $t('login.email') }}
            </label>
            <input
              id="email"
              type="email"
              required
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
              placeholder="...@ipleiria.pt"
            />
          </div>

          <div>
            <div class="mb-1.5 flex items-center justify-between">
              <label for="password" class="block text-sm font-semibold text-[color:var(--text)]">
                {{ $t('login.password') }}
              </label>
              <a
                href="#"
                class="text-xs font-medium text-[color:var(--muted)] transition-colors duration-200 hover:text-[color:var(--primary)]"
                >{{ $t('login.forgot') }}</a
              >
            </div>
            <input
              id="password"
              type="password"
              required
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
              placeholder="••••••••••••"
            />
          </div>

          <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5 text-sm font-semibold transition-colors duration-150 bg-neutral-900 dark:bg-neutral-200 text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-300 border border-neutral-800 dark:border-neutral-300 w-full">{{ $t('login.submit') }}</button>
        </form>
      </section>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const handleLogin = () => {
  router.push('/')
}
</script>
