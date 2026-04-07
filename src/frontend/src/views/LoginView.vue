<template>
  <div class="page-container page-section animate-fade-in-up">
    <div class="mx-auto max-w-md">
      <section class="simple-card p-7 sm:p-8">
        <div class="mb-8 text-center">
          <p class="section-kicker">{{ $t('login.kicker') }}</p>
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
              class="simple-input"
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
              class="simple-input"
              placeholder="••••••••••••"
            />
          </div>

          <button type="submit" class="simple-button w-full">{{ $t('login.submit') }}</button>
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
