<script setup></script>

<template>
  <footer class="mt-auto border-t border-t-neutral-200 dark:border-t-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-7 sm:py-8">
      <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
        <div class="space-y-2">
          <p class="text-base font-semibold">
            {{ $t('brand.name') }} · {{ $t('brand.institution') }}
          </p>
          <p class="max-w-xl text-sm">{{ $t('footer.description') }}</p>
          <p class="text-xs">
            © {{ currentYear }} {{ $t('brand.name') }}. {{ $t('footer.rights') }}
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-5">
          <a
            href="#"
            class="text-sm transition-colors duration-200 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100"
            >{{ $t('footer.terms') }}</a
          >
          <a
            href="#"
            class="text-sm transition-colors duration-200 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100"
            >{{ $t('footer.privacy') }}</a
          >
          <a
            href="#"
            class="text-sm transition-colors duration-200 text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100"
            >{{ $t('footer.support') }}</a
          >
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped></style>
