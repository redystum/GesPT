<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up">
    <header class="mb-8 md:flex md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">
          {{ $t('admin.kicker') }}
        </p>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
          {{ $t('admin.title') }}
        </h1>
        <p class="mt-2 text-sm sm:text-base text-neutral-500 dark:text-neutral-400">
          {{ $t('admin.subtitle') }}
        </p>
      </div>
    </header>

    <div class="flex flex-col xl:flex-row gap-8 lg:gap-10">
      <aside class="w-full xl:w-64 shrink-0 xl:border-r border-neutral-200 dark:border-neutral-800 xl:pr-8">
        <nav class="flex xl:flex-col gap-2 overflow-x-auto pb-5 xl:pb-0 hide-scrollbar border-b border-neutral-200 dark:border-neutral-800 xl:border-b-0">
          <a
            v-for="tab in tabs"
            :key="tab.id"
            href="#"
            @click.prevent="activeTab = tab.id"
            :class="[
              'flex items-center whitespace-nowrap rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all border',
              activeTab === tab.id
                ? 'bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 shadow-sm border-neutral-200 dark:border-neutral-800'
                : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800/50 hover:text-neutral-900 dark:hover:text-neutral-100 border-transparent'
            ]"
          >
            {{ $t(tab.translationKey) }}
          </a>
        </nav>
      </aside>

      <main class="flex-1 min-w-0">
        <DashboardView v-if="activeTab === 'dashboard'" />
        <LoginView v-else-if="activeTab === 'auth'" />
        <RolesView v-else-if="activeTab === 'roles'" />
        <div v-else class="flex flex-col items-center justify-center h-[500px] rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700 bg-neutral-50/50 dark:bg-neutral-800/10">
          <font-awesome-icon :icon="['fas', 'hammer']" class="text-4xl text-neutral-300 dark:text-neutral-600 mb-4" />
          <h2 class="text-lg font-bold text-neutral-600 dark:text-neutral-400">{{ $t(tabs.find(t => t.id === activeTab)?.translationKey || '') }}</h2>
          <p class="text-sm text-neutral-500 dark:text-neutral-500 mt-1">{{ $t('common.under_construction') || 'Under construction' }}</p>
        </div>
      </main>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import DashboardView from '@/components/Admin/DashboardView.vue'
import LoginView from '@/components/Admin/LoginView.vue'
import RolesView from '@/components/Admin/RolesView.vue'

const authStore = useAuthStore()

const activeTab = ref('')

const tabs = computed(() => {
  const list = []
  const hasAdmin = authStore.hasPermission([
    'roles.view_any',
    'users.view_any',
    'login_dominios.view_any'
  ])

  if (hasAdmin) {
    list.push({ id: 'dashboard', translationKey: 'admin.menu_dashboard' })
  }

  if (authStore.hasPermission(['login_dominios.view_any', 'users.view_any'])) {
    list.push({ id: 'auth', translationKey: 'admin.menu_auth' })
  }

  if (authStore.hasPermission('roles.view_any')) {
    list.push({ id: 'roles', translationKey: 'admin.menu_roles' })
  }

  if (hasAdmin) {
    list.push({ id: 'audit', translationKey: 'admin.menu_audit' })
  }

  return list
})

onMounted(() => {
  if (tabs.value.length > 0) {
    activeTab.value = tabs.value[0].id
  }
})
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
