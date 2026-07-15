<template>
  <article
    class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-xs flex flex-col overflow-hidden"
  >
    <div
      class="px-6 py-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between gap-4"
    >
      <div>
        <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
          {{ planos.length }} {{ $t('course_detail.plans_table_title') }}
        </h2>
      </div>
      <router-link v-if="authStore.hasPermission('planos_estudo.create')" :to="{ name: 'wizard'}"
        class="inline-flex items-center justify-center gap-1.5 rounded-md px-3.5 py-1.5 text-xs font-semibold transition-all bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 hover:bg-neutral-800 dark:hover:bg-neutral-200 cursor-pointer"
      >
        <font-awesome-icon :icon="['fas', 'plus']" class="text-[10px]" />
        {{ $t('course_detail.add_plan') }}
      </router-link>
    </div>

    <!-- Search Control -->
    <div class="px-6 py-3 bg-neutral-50/50 dark:bg-neutral-800/10 border-b border-neutral-100 dark:border-neutral-800/60 flex gap-3 items-center">
      <div class="relative w-full">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-neutral-400">
          <font-awesome-icon :icon="['fas', 'search']" class="text-xs" />
        </span>
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('course_detail.search_plans_placeholder')"
          class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-neutral-900 dark:text-neutral-100 focus:border-neutral-400 focus:ring-2 focus:ring-neutral-500/10 focus:outline-hidden transition-all placeholder-neutral-400 dark:placeholder-neutral-500"
        />
      </div>
    </div>

    <!-- Loader for Planos -->
    <div v-if="loading" class="p-8 flex justify-center items-center">
      <font-awesome-icon :icon="['fas', 'circle-notch']" spin class="text-2xl text-neutral-400" />
    </div>

    <div v-else-if="filteredPlanos.length === 0" class="p-8 text-center flex flex-col items-center">
      <div
        class="w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 mb-3"
      >
        <font-awesome-icon :icon="['fas', 'scroll']" class="text-lg" />
      </div>
      <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300">
        {{ planos.length === 0 ? $t('course_detail.no_plans') : $t('common.no_results') || 'No results found' }}
      </h3>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm border-collapse text-left">
        <thead>
          <tr class="bg-neutral-50 dark:bg-neutral-800/40 text-neutral-500 dark:text-neutral-400 text-xs font-semibold border-b border-neutral-100 dark:border-neutral-800">
            <th class="px-6 py-4">{{ $t('wizard.plan_step.academic_year') }}</th>
            <th class="px-6 py-4 text-center">{{ $t('course_detail.ects') }}</th>
            <th class="px-6 py-4 text-center">{{ $t('course_detail.ucs') }}</th>
            <th class="px-6 py-4 text-right">{{ $t('course_detail.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/60">
          <tr
            v-for="plan in filteredPlanos"
            :key="plan.id"
            class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/20 transition-colors"
          >
            <td class="px-6 py-3.5 font-semibold text-neutral-900 dark:text-neutral-100">
              {{ plan.ano }}
            </td>
            <td class="px-6 py-3.5 text-center text-neutral-700 dark:text-neutral-300">
              {{ plan.ects }}
            </td>
            <td class="px-6 py-3.5 text-center text-neutral-700 dark:text-neutral-300">
              {{ plan.ucs }}
            </td>
            <td class="px-6 py-3.5 text-right whitespace-nowrap">
              <div class="inline-flex gap-2">
                <router-link
                  :to="{ path: '/compare', query: { course: courseId, plan: plan.id } }"
                  class="p-1.5 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                  title="Compare/View Equivalences"
                >
                  <font-awesome-icon :icon="['fas', 'eye']" class="w-3.5 h-3.5" />
                </router-link>
                <router-link
                  v-if="authStore.hasPermission('planos_estudo.update')"
                  :to="{ name: 'plan-edit-wizard', params: { id: plan.id } }"
                  class="p-1.5 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                  :title="$t('course_detail.edit_plan')"
                >
                  <font-awesome-icon :icon="['fas', 'pencil']" class="w-3.5 h-3.5" />
                </router-link>
                <button
                  v-if="authStore.hasPermission('planos_estudo.delete')"
                  @click="$emit('delete-plan', plan)"
                  class="p-1.5 rounded-md text-neutral-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 cursor-pointer"
                  :title="$t('course_detail.delete_plan')"
                >
                  <font-awesome-icon :icon="['fas', 'trash']" class="w-3.5 h-3.5" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  planos: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  courseId: {
    type: [Number, String],
    required: true
  }
})

defineEmits(['delete-plan'])

const authStore = useAuthStore()
const searchQuery = ref('')

const filteredPlanos = computed(() => {
  let result = props.planos

  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    result = result.filter(plan => {
      const idStr = String(plan.id)
      const anoStr = (plan.ano || '').toLowerCase()
      return idStr.includes(query) || anoStr.includes(query)
    })
  }

  return result
})
</script>
