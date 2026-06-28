<template>
  <div class="h-full flex flex-col">
    <section class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-sm flex flex-col h-[550px] sm:h-[650px] xl:h-[calc(100vh-16rem)] min-h-[400px] w-full">
      <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 border-b border-neutral-100 dark:border-neutral-800 shrink-0">
        <div>
          <h2 class="text-base font-bold text-neutral-900 dark:text-neutral-100">{{ $t('admin.audit_title') }}</h2>
          <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $t('admin.audit_subtitle') }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-center">
          <div class="relative w-full sm:w-auto">
            <select
              v-model="activeFilter"
              @change="fetchLogs(true)"
              class="w-full sm:w-64 rounded-lg border border-neutral-300 dark:border-neutral-700 px-3 py-2 text-sm bg-neutral-50 dark:bg-neutral-900/50 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-100 focus:ring-1 focus:ring-neutral-900 dark:focus:ring-neutral-100 focus:outline-none transition-shadow cursor-pointer"
            >
              <option value="all">{{ $t('admin.audit_filter_all') }}</option>
              <option value="database">{{ $t('admin.audit_filter_database') }}</option>
              <option value="access_control">{{ $t('admin.audit_filter_access') }}</option>
              <option value="failed_login">{{ $t('admin.audit_filter_failed_login') }}</option>
              <option value="blocked">{{ $t('admin.audit_filter_blocked') }}</option>
              <option value="unauthorized">{{ $t('admin.audit_filter_unauthorized') }}</option>
            </select>
          </div>
          <button
            @click="fetchLogs(true)"
            :disabled="isLoading"
            class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50 hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed w-10 h-10 shrink-0"
            :title="$t('common.refresh') || 'Refresh'"
          >
            <font-awesome-icon :icon="['fas', 'rotate']" :spin="isLoading" />
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4">
        <div v-if="isLoading && logs.length === 0" class="flex flex-col items-center justify-center py-12 text-neutral-500">
          <font-awesome-icon :icon="['fas', 'spinner']" spin class="text-3xl mb-3 text-neutral-400" />
          <p class="text-sm">Loading audit logs...</p>
        </div>

        <div v-else-if="logs.length === 0" class="flex flex-col items-center justify-center py-16 text-neutral-400 border border-dashed border-neutral-200 dark:border-neutral-800 rounded-xl bg-neutral-50/50 dark:bg-neutral-900/20">
          <font-awesome-icon :icon="['fas', 'clipboard-list']" class="text-4xl mb-3 text-neutral-300 dark:text-neutral-700" />
          <p class="text-sm font-medium">{{ $t('admin.audit_no_logs') }}</p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="log in logs"
            :key="log.id"
            class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-800/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all hover:border-neutral-300 dark:hover:border-neutral-700 hover:shadow-sm"
          >
            <div class="flex items-center gap-3">
              <div
                :class="[
                  'w-9 h-9 rounded-lg flex items-center justify-center shrink-0 text-sm',
                  getIconClass(log)
                ]"
              >
                <font-awesome-icon :icon="getIcon(log)" />
              </div>
              <div class="space-y-1.5">
                <p class="text-sm text-neutral-800 dark:text-neutral-200 leading-relaxed font-medium break-words">
                  <span v-html="renderMarkdown(log.sentence)"></span>
                </p>
                
                <!-- Expandable details for permissions additions/removals -->
                <div v-if="log.details && log.details.length > 0" class="space-y-2">
                  <button
                    @click="toggleDetails(log.id)"
                    class="text-xs font-semibold text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-200 flex items-center gap-1 cursor-pointer"
                  >
                    <font-awesome-icon :icon="['fas', expandedLogs[log.id] ? 'chevron-up' : 'chevron-down']" class="text-[10px]" />
                    {{ expandedLogs[log.id] ? $t('admin.audit_details_collapse') : $t('admin.audit_details_expand') }}
                  </button>
                  <div
                    v-if="expandedLogs[log.id]"
                    class="pl-4 border-l-2 border-neutral-200 dark:border-neutral-800 space-y-1 text-xs text-neutral-500 dark:text-neutral-400 py-1"
                  >
                    <div v-for="(detail, idx) in log.details" :key="idx" class="flex items-center gap-2">
                      <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                      <span>{{ detail }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="shrink-0 text-right text-xs text-neutral-400 dark:text-neutral-500 self-end sm:self-auto">
              <span>{{ formatTimeAgo(log.created_at) }}</span>
            </div>
          </div>
        </div>

      </div>

      <footer
        class="flex items-center justify-between border-t border-neutral-200 dark:border-neutral-800 px-6 py-4 bg-neutral-50/50 dark:bg-neutral-800/10 shrink-0"
      >
        <span class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">
          {{ pagination.from || 0 }} - {{ pagination.to || 0 }} {{ $t('admin.of') }}
          {{ pagination.total || 0 }} {{ $t('admin.total_logs') }}
        </span>
        <div class="flex items-center gap-2">
          <button
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            :disabled="!pagination.prev_page_url"
            @click="fetchPage(pagination.current_page - 1)"
          >
            <font-awesome-icon :icon="['fas', 'chevron-left']" class="text-xs" />
          </button>
          <button
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            :disabled="!pagination.next_page_url"
            @click="fetchPage(pagination.current_page + 1)"
          >
            <font-awesome-icon :icon="['fas', 'chevron-right']" class="text-xs" />
          </button>
        </div>
      </footer>
    </section>
  </div>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue'

const api = inject('api')

const logs = ref([])
const activeFilter = ref('all')
const isLoading = ref(false)
const expandedLogs = ref({})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 200,
  total: 0,
  from: 0,
  to: 0,
  prev_page_url: null,
  next_page_url: null,
})

const fetchLogs = async (reset = false, page = 1) => {
  if (reset) {
    logs.value = []
    expandedLogs.value = {}
  }
  
  isLoading.value = true
  try {
    const response = await api.get('/admin/audit-logs', {
      params: {
        filter: activeFilter.value,
        page: page
      }
    })
    
    if (response.data && response.data.data) {
      logs.value = response.data.data
      
      if (response.data.meta) {
        pagination.value = {
          current_page: response.data.meta.current_page,
          last_page: response.data.meta.last_page,
          per_page: response.data.meta.per_page,
          total: response.data.meta.total,
          from: response.data.meta.from,
          to: response.data.meta.to,
          prev_page_url: response.data.links?.prev,
          next_page_url: response.data.links?.next,
        }
      } else {
        pagination.value = {
          current_page: response.data.current_page || 1,
          last_page: response.data.last_page || 1,
          per_page: response.data.per_page || 200,
          total: response.data.total || logs.value.length,
          from: response.data.from || (logs.value.length ? 1 : 0),
          to: response.data.to || logs.value.length,
          prev_page_url: response.data.prev_page_url,
          next_page_url: response.data.next_page_url,
        }
      }
    }
  } catch (error) {
    console.error('Failed to fetch audit logs:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchLogs(false, page)
  }
}

const toggleDetails = (id) => {
  expandedLogs.value[id] = !expandedLogs.value[id]
}

const renderMarkdown = (text) => {
  if (!text) return ''
  // Replace markdown links [Name](Url) with HTML links <a href="Url" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Name</a>
  return text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">$1</a>')
}

const getIcon = (log) => {
  const name = log.log_name
  const event = log.event

  if (name === 'login_failed') {
    return ['fas', 'shield-halved']
  }
  if (name === 'user_roles' || name === 'user_permissions') {
    return ['fas', 'user-shield']
  }
  if (event === 'created') {
    return ['fas', 'plus']
  }
  if (event === 'updated') {
    return ['fas', 'pen']
  }
  if (event === 'deleted') {
    return ['fas', 'trash']
  }
  return ['fas', 'database']
}

const getIconClass = (log) => {
  const name = log.log_name
  const event = log.event

  if (name === 'login_failed') {
    return 'bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/30'
  }
  if (name === 'user_roles' || name === 'user_permissions') {
    return 'bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30'
  }
  if (event === 'created') {
    return 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/20'
  }
  if (event === 'updated') {
    return 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/20'
  }
  if (event === 'deleted') {
    return 'bg-orange-50 dark:bg-orange-950/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-900/20'
  }
  return 'bg-neutral-50 dark:bg-neutral-900/40 text-neutral-600 dark:text-neutral-400 border border-neutral-100 dark:border-neutral-800'
}

const formatTimeAgo = (isoString) => {
  if (!isoString) return ''
  const date = new Date(isoString)
  const now = new Date()
  const diffMs = now - date
  const diffMin = Math.floor(diffMs / 60000)
  
  if (diffMin < 1) return 'Just now'
  if (diffMin < 60) return `${diffMin}m ago`
  
  const diffHrs = Math.floor(diffMin / 60)
  if (diffHrs < 24) return `${diffHrs}h ago`
  
  const diffDays = Math.floor(diffHrs / 24)
  if (diffDays === 1) return 'Yesterday'
  if (diffDays < 7) return `${diffDays}d ago`
  
  return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(() => {
  fetchLogs()
})
</script>

<style scoped>
</style>
