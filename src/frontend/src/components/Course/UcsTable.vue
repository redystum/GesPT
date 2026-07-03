<template>
  <article
    class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-xs flex flex-col overflow-hidden"
  >
    <div
      class="px-6 py-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between gap-4"
    >
      <div>
        <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
          {{ $t('course_detail.ucs_table_title') }}
        </h2>
        <p class="text-xs text-neutral-400 mt-0.5">
          <span v-if="filteredUcs.length !== ucs.length">
            {{ $t('course_detail.showing_filtered_ucs', { count: filteredUcs.length, total: ucs.length }) }}
          </span>
          <span v-else>
            {{ ucs.length }} {{ $t('course_detail.ucs_table_title').toLowerCase() }}
          </span>
        </p>
      </div>
      <button
        v-if="authStore.hasPermission('ucs.create')"
        @click="$emit('add-uc')"
        class="inline-flex items-center justify-center gap-1.5 rounded-md px-3.5 py-1.5 text-xs font-semibold transition-all bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 hover:bg-neutral-800 dark:hover:bg-neutral-200 cursor-pointer"
      >
        <font-awesome-icon :icon="['fas', 'plus']" class="text-[10px]" />
        {{ $t('course_detail.add_uc') }}
      </button>
    </div>

    <!-- Search & Filter Controls -->
    <div class="px-6 py-3 bg-neutral-50/50 dark:bg-neutral-800/10 border-b border-neutral-100 dark:border-neutral-800/60 flex flex-col sm:flex-row gap-3 items-center justify-between">
      <div class="relative w-full sm:flex-1">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-neutral-400">
          <font-awesome-icon :icon="['fas', 'search']" class="text-xs" />
        </span>
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('course_detail.search_ucs_placeholder')"
          class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-neutral-900 dark:text-neutral-100 focus:border-neutral-400 focus:ring-2 focus:ring-neutral-500/10 focus:outline-hidden transition-all placeholder-neutral-400 dark:placeholder-neutral-500"
        />
      </div>
      <div class="relative w-full sm:w-auto shrink-0">
        <select
          v-model="activeFilter"
          class="w-full sm:w-48 rounded-lg border border-neutral-200 dark:border-neutral-700 px-3 py-1.5 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 focus:border-neutral-400 focus:ring-2 focus:ring-neutral-500/10 focus:outline-hidden transition-all cursor-pointer"
        >
          <option value="all">{{ $t('course_detail.filter_all') }}</option>
          <option value="on_plan">{{ $t('course_detail.filter_on_plan') }}</option>
          <option value="not_on_plan">{{ $t('course_detail.filter_not_on_plan') }}</option>
        </select>
      </div>
    </div>

    <!-- Loader for UCs -->
    <div v-if="loading" class="p-8 flex justify-center items-center">
      <font-awesome-icon :icon="['fas', 'circle-notch']" spin class="text-2xl text-neutral-400" />
    </div>

    <div v-else-if="filteredUcs.length === 0" class="p-8 text-center flex flex-col items-center">
      <div
        class="w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 mb-3"
      >
        <font-awesome-icon :icon="['fas', 'book-open']" class="text-lg" />
      </div>
      <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300">
        {{ ucs.length === 0 ? $t('course_detail.no_ucs') : $t('common.no_results') || 'No results found' }}
      </h3>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm border-collapse text-left">
        <thead>
          <tr class="bg-neutral-50 dark:bg-neutral-800/40 text-neutral-500 dark:text-neutral-400 text-xs font-semibold border-b border-neutral-100 dark:border-neutral-800">
            <th class="px-6 py-4">{{ $t('wizard.uc_step.year') }}</th>
            <th class="px-6 py-4">{{ $t('course_detail.pt_name') }}</th>
            <th class="px-6 py-4">{{ $t('course_detail.acronym') }}</th>
            <th class="px-6 py-4 text-center">{{ $t('course_detail.ects') }}</th>
            <th class="px-6 py-4 text-center">{{ $t('course_detail.semester') }}</th>
            <th class="px-6 py-4 text-center">{{ $t('course_detail.hours') }}</th>
            <th class="px-6 py-4 text-right">{{ $t('course_detail.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/60">
          <tr
            v-for="uc in filteredUcs"
            :key="uc.id"
            class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/20 transition-colors"
          >
            <td class="px-6 py-3.5 font-medium text-neutral-600 dark:text-neutral-400">
              {{ uc.ano ? `${uc.ano}º` : 'N/A' }}
            </td>
            <td class="px-6 py-3.5 font-semibold text-neutral-900 dark:text-neutral-100">
              <div>
                <router-link
                  v-if="authStore.hasPermission('ucs.view')"
                  :to="{ name: 'uc-detail', params: { id: uc.id } }"
                  class="hover:underline hover:text-neutral-600 dark:hover:text-neutral-300"
                >
                  {{ locale === 'pt' ? uc.nome : (uc.name || uc.nome) }}
                </router-link>
                <span v-else>{{ locale === 'pt' ? uc.nome : (uc.name || uc.nome) }}</span>
                <p v-if="locale === 'pt' && uc.name" class="text-[11px] font-normal text-neutral-400 dark:text-neutral-500">
                  {{ uc.name }}
                </p>
                <p v-else-if="locale === 'en' && uc.name && uc.nome !== uc.name" class="text-[11px] font-normal text-neutral-400 dark:text-neutral-500">
                  {{ uc.nome }}
                </p>
              </div>
            </td>
            <td class="px-6 py-3.5">
              <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400">
                {{ uc.sigla }}
              </span>
            </td>
            <td class="px-6 py-3.5 text-center text-neutral-800 dark:text-neutral-200">
              {{ uc.ects }}
            </td>
            <td class="px-6 py-3.5 text-center text-neutral-600 dark:text-neutral-300">
              {{ uc.semestre ? `${uc.semestre}º` : 'N/A' }}
            </td>
            <td class="px-6 py-3.5 text-center text-neutral-600 dark:text-neutral-300">
              {{ uc.horas }}h
            </td>
            <td class="px-6 py-3.5 text-right whitespace-nowrap">
              <div class="inline-flex items-center gap-2">
                <!-- Plan status indicator -->
                <router-link
                  :to="{ name: 'uc-detail', params: { id: uc.id } }"
                  class="p-1.5 rounded-md text-xs font-semibold"
                  :class="uc.plano_ucs_count > 0 ? 'text-emerald-500' : 'text-neutral-300 dark:text-neutral-700'"
                  :title="uc.plano_ucs_count > 0 ? $t('course_detail.in_plan') : $t('course_detail.not_in_plan')"
                >
                  <font-awesome-icon :icon="['fas', 'scroll']" class="w-3.5 h-3.5" />
                </router-link>

                <router-link
                  v-if="authStore.hasPermission('ucs.update')"
                  :to="{ name: 'uc-detail', params: { id: uc.id } }"
                  class="p-1.5 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer"
                  :title="$t('course_detail.edit_uc')"
                >
                  <font-awesome-icon :icon="['fas', 'pencil']" class="w-3.5 h-3.5" />
                </router-link>
                <button
                  v-if="authStore.hasPermission('ucs.delete')"
                  @click="$emit('delete-uc', uc)"
                  class="p-1.5 rounded-md text-neutral-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 cursor-pointer"
                  :title="$t('course_detail.delete_uc')"
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
import { useI18n } from '@/i18n'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  ucs: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  }
})

defineEmits(['add-uc', 'delete-uc'])

const { locale } = useI18n()
const authStore = useAuthStore()

const searchQuery = ref('')
const activeFilter = ref('all')

const filteredUcs = computed(() => {
  let result = props.ucs

  // 1. Filter by plan status
  if (activeFilter.value === 'on_plan') {
    result = result.filter(uc => uc.plano_ucs_count > 0)
  } else if (activeFilter.value === 'not_on_plan') {
    result = result.filter(uc => !uc.plano_ucs_count || uc.plano_ucs_count === 0)
  }

  // 2. Filter by search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    result = result.filter(uc => {
      const nomePt = (uc.nome || '').toLowerCase()
      const nomeEn = (uc.name || '').toLowerCase()
      const sigla = (uc.sigla || '').toLowerCase()
      return nomePt.includes(query) || nomeEn.includes(query) || sigla.includes(query)
    })
  }

  // 3. Sort by year and then semester (preserving original sorting logic)
  return [...result].sort((a, b) => {
    if (a.ano !== b.ano) {
      return (a.ano || 99) - (b.ano || 99)
    }
    return (a.semestre || 99) - (b.semestre || 99)
  })
})
</script>
