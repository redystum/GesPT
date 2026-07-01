<template>
  <section class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <article
        v-for="card in statCards"
        :key="card.key"
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] p-5 shadow-sm"
      >
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $t(card.labelKey) }}</p>
            <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ formatNumber(card.value) }}</p>
          </div>
          <span
            :class="[
              'flex h-11 w-11 items-center justify-center rounded-lg',
              card.iconBg,
              card.iconColor
            ]"
          >
            <font-awesome-icon :icon="card.icon" class="text-lg" />
          </span>
        </div>
      </article>
    </div>

    <div v-if="isLoading" class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] p-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
      {{ $t('admin.dashboard_loading') }}
    </div>

    <div v-else-if="errorMessage" class="rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-950/20 p-4 text-sm text-red-700 dark:text-red-300">
      {{ errorMessage }}
    </div>

    <div v-else class="grid grid-cols-1 gap-6">
      <section class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] p-5 shadow-sm">
        <div class="mb-5">
          <h2 class="text-base font-bold text-neutral-900 dark:text-neutral-100">{{ $t('admin.dashboard_users_by_role') }}</h2>
          <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $t('admin.dashboard_users_by_role_subtitle') }}</p>
        </div>

        <div v-if="usersByRole.length" class="flex flex-col sm:flex-row 2xl:flex-col items-center gap-6">
          <div class="relative h-44 w-44 shrink-0">
            <svg viewBox="0 0 120 120" class="h-full w-full -rotate-90">
              <circle cx="60" cy="60" r="44" fill="none" stroke="currentColor" stroke-width="18" class="text-neutral-100 dark:text-neutral-800" />
              <circle
                v-for="segment in roleSegments"
                :key="segment.label"
                cx="60"
                cy="60"
                r="44"
                fill="none"
                :stroke="segment.color"
                stroke-width="18"
                stroke-linecap="butt"
                :stroke-dasharray="`${segment.length} ${donutCircumference - segment.length}`"
                :stroke-dashoffset="segment.offset"
              />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
              <span class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ formatNumber(totalRoleUsers) }}</span>
              <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ $t('admin.dashboard_users') }}</span>
            </div>
          </div>

          <div class="w-full space-y-3">
            <div v-for="segment in roleSegments" :key="segment.label" class="flex items-center justify-between gap-4 text-sm">
              <div class="flex min-w-0 items-center gap-2">
                <span class="h-3 w-3 shrink-0 rounded-full" :style="{ backgroundColor: segment.color }"></span>
                <span class="truncate font-medium text-neutral-700 dark:text-neutral-300" :title="segment.label">{{ segment.label }}</span>
              </div>
              <span class="font-semibold text-neutral-900 dark:text-neutral-100">{{ formatNumber(segment.value) }}</span>
            </div>
          </div>
        </div>
        <p v-else class="py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">{{ $t('admin.dashboard_no_role_data') }}</p>
      </section>
    </div>
  </section>
</template>

<script setup>
import { computed, inject, onMounted, ref } from 'vue'

const api = inject('api')

const isLoading = ref(true)
const errorMessage = ref('')
const stats = ref({
  courses: 0,
  study_plans: 0,
  users: 0,
  admins: 0
})
const charts = ref({
  users_by_role: []
})

const statCards = computed(() => [
  {
    key: 'courses',
    labelKey: 'admin.dashboard_courses',
    value: stats.value.courses,
    icon: ['fas', 'graduation-cap'],
    iconBg: 'bg-emerald-50 dark:bg-emerald-950/40',
    iconColor: 'text-emerald-600 dark:text-emerald-300'
  },
  {
    key: 'study_plans',
    labelKey: 'admin.dashboard_study_plans',
    value: stats.value.study_plans,
    icon: ['fas', 'layer-group'],
    iconBg: 'bg-sky-50 dark:bg-sky-950/40',
    iconColor: 'text-sky-600 dark:text-sky-300'
  },
  {
    key: 'users',
    labelKey: 'admin.dashboard_total_users',
    value: stats.value.users,
    icon: ['fas', 'users'],
    iconBg: 'bg-amber-50 dark:bg-amber-950/40',
    iconColor: 'text-amber-600 dark:text-amber-300'
  },
  {
    key: 'admins',
    labelKey: 'admin.dashboard_admins',
    value: stats.value.admins,
    icon: ['fas', 'user-shield'],
    iconBg: 'bg-rose-50 dark:bg-rose-950/40',
    iconColor: 'text-rose-600 dark:text-rose-300'
  }
])

const usersByRole = computed(() => charts.value.users_by_role ?? [])
const totalRoleUsers = computed(() => usersByRole.value.reduce((total, item) => total + (Number(item.value) || 0), 0))
const donutCircumference = 2 * Math.PI * 44
const chartColors = ['#10b981', '#0ea5e9', '#f59e0b', '#e11d48', '#8b5cf6', '#14b8a6', '#64748b']

const roleSegments = computed(() => {
  let usedLength = 0
  const total = totalRoleUsers.value || 1

  return usersByRole.value.map((item, index) => {
    const length = ((Number(item.value) || 0) / total) * donutCircumference
    const segment = {
      label: item.label,
      value: item.value,
      color: chartColors[index % chartColors.length],
      length,
      offset: -usedLength
    }

    usedLength += length
    return segment
  })
})

const formatNumber = (value) => new Intl.NumberFormat('pt-PT').format(Number(value) || 0)

const fetchDashboard = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get('/admin/dashboard')
    stats.value = response.data.data.stats
    charts.value = response.data.data.charts
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Não foi possível carregar a visão geral.'
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchDashboard)
</script>
