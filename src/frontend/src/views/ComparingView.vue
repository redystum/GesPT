<template>
  <div
    class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up"
  >
    <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <p
          class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400"
        >
          {{ $t('compare.kicker') }}
        </p>
        <h1
          class="text-2xl sm:text-3xl font-bold leading-tight text-neutral-900 dark:text-neutral-100 mt-2"
        >
          {{ $t('compare.title') }}
        </h1>
        <p class="mt-2 text-sm sm:text-base text-neutral-500 dark:text-neutral-400">
          {{ $t('compare.subtitle') }}
        </p>
      </div>

      <section
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 w-full p-5 lg:w-auto"
      >
        <div class="grid grid-cols-1 gap-4">
<!--        md:grid-cols-2">-->
<!--          <div class="min-w-56">-->
<!--            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.12em]">-->
<!--              {{ $t('compare.student') }}-->
<!--            </label>-->
<!--            <select-->
<!--              v-model="estudante_id"-->
<!--              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"-->
<!--            >-->
<!--              <option v-for="n in 3" :key="n" :value="n">-->
<!--                {{ $t('compare.student') }} {{ n }}-->
<!--              </option>-->
<!--            </select>-->
<!--          </div>-->

          <div class="min-w-56">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.12em]">
              {{ $t('compare.course') }}
            </label>
            <select
              v-model="curso_id"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
            >
              <option v-for="c in cursos" :key="c.id" :value="c.id">
                {{ locale === 'pt' ? c.nome : c.name }}
              </option>
            </select>
          </div>
        </div>
      </section>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
      <article
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-5"
      >
        <p
          class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400"
        >
          {{ $t('compare.no_antecedente') }}
        </p>
        <p class="mt-2 text-3xl font-bold">{{ statsNoAntecedente }}</p>
      </article>

      <article
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-5"
      >
        <p
          class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400"
        >
          {{ $t('compare.descontinuadas') }}
        </p>
        <p class="mt-2 text-3xl font-bold">{{ statsDescontinuadas }}</p>
      </article>

      <article
        class="rounded-lg border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] transition-colors duration-150 p-5"
      >
        <p
          class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400"
        >
          {{ $t('compare.equivalences') }}
        </p>
        <p class="mt-2 text-3xl font-bold">{{ statsEquivalences }}</p>
      </article>
    </div>

    <section
      v-if="isReady"
      class="overflow-hidden rounded-md border-2 border-neutral-300 dark:border-neutral-700 bg-white dark:bg-[#1a1a1a]"
    >
      <div
        class="border-b-2 border-neutral-300 dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-800 grid grid-cols-1 p-4 sm:grid-cols-2 sm:gap-4"
      >
        <div class="space-y-1">
          <label
            class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400"
            >{{ $t('compare.initial_plan') }}</label
          >
          <select
            v-model="plano_inicial"
            class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
          >
            <option :value="null" disabled>&mdash;</option>
            <option v-for="p in planos_disponiveis" :key="p.id" :value="p.id">{{ p.ano }}</option>
          </select>
        </div>

        <div class="space-y-1">
          <label
            class="text-[0.68rem] sm:text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500 dark:text-neutral-400"
            >{{ $t('compare.target_plan') }}</label
          >
          <select
            v-model="plano_final"
            class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
          >
            <option :value="null" disabled>&mdash;</option>
            <option v-for="p in planos_disponiveis" :key="p.id" :value="p.id">{{ p.ano }}</option>
          </select>
        </div>
      </div>

      <div
        v-if="equivalences.length === 0 && plano_inicial && plano_final"
        class="flex items-start gap-3 rounded-md border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 m-4"
      >
        <div>
          <p class="font-semibold">{{ $t('states.empty_title') }}</p>
          <p class="text-sm">{{ $t('compare.empty_hint') }}</p>
        </div>
      </div>

      <CompareTable
        :equivalences="equivalences"
        :plano_final="plano_final"
      />
    </section>

    <div
      v-else
      class="flex items-start gap-3 rounded-md border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400"
    >
      <div>
        <p class="font-semibold">{{ $t('compare.select_prompt_title') }}</p>
        <p class="text-sm">{{ $t('compare.select_prompt_subtitle') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, inject, onMounted, ref, watch } from 'vue'
import { useI18n } from '@/i18n'
import { useRoute } from 'vue-router'
import CompareTable from '@/components/CompareTable.vue'

const { locale } = useI18n()
const route = useRoute()
const api = inject('api')

const estudante_id = ref(1)
const curso_id = ref(null)
const plano_inicial = ref(null)
const plano_final = ref(null)
const data = ref([])
const cursos = ref([])
const equivalences = ref([])

const isReady = computed(() => Boolean(curso_id.value))

const statsNoAntecedente = computed(() => {
  return equivalences.value.filter((item) => !item.inicial.some((p) => p.data)).length
})

const statsDescontinuadas = computed(() => {
  return equivalences.value.filter((item) => !item.final.some((p) => p.data)).length
})

const statsEquivalences = computed(() => {
  return equivalences.value.filter((item) => {
    const initialCount = item.inicial.filter((p) => p.data).length
    const finalCount = item.final.filter((p) => p.data).length
    return initialCount > 0 && finalCount > 0 && (initialCount > 1 || finalCount > 1)
  }).length
})

const planos_disponiveis = computed(() => {
  const curso = cursos.value.find((c) => c.id === curso_id.value)
  return curso ? curso.planos_estudo : []
})

async function fetchCursos() {
  try {
    const response = await api.get('/cursos', {
      params: {
        with_planos_estudo: 1
      }
    })
    const res = response.data
    cursos.value = res.data

    if (cursos.value.length > 0) {
      if (route.query.course && cursos.value.some((c) => c.id == route.query.course)) {
        curso_id.value = Number(route.query.course)
      }

      if (planos_disponiveis.value.length > 0 && curso_id.value) {
        if (route.query.plan && planos_disponiveis.value.some((p) => p.id == route.query.plan)) {
          plano_inicial.value = Number(route.query.plan)
        }
      }
    }
  } catch (error) {
    console.error(error)
  }
}

async function fetchData() {
  if (!plano_inicial.value) {
    equivalences.value = []
    data.value = []
    return
  }

  try {
    const params = {
      estudante_id: estudante_id.value,
      plano_inicial: plano_inicial.value
    }

    if (plano_final.value) {
      params.plano_final = plano_final.value
    }

    const response = await api.get('/compare', { params })
    const payload = response.data
    data.value = payload.data
    formatData()
  } catch (error) {
    console.error(error)
  }
}

function formatData() {
  equivalences.value = data.value.map((item) => ({
    inicial: formatPart(item, 'inicial'),
    final: formatPart(item, 'final')
  }))
}

function formatPart(item, side) {
  if (side !== 'inicial' && side !== 'final') {
    throw new Error('Invalid side')
  }

  if (!item[side]) {
    return [{ data: null }]
  }

  return item[side].map((entry) => ({
    data: entry.uc ? entry : null
  }))
}

onMounted(async () => {
  await fetchCursos()
  await fetchData()
})

watch([estudante_id, plano_inicial, plano_final], fetchData)
watch(curso_id, (newVal, oldVal) => {
  if (oldVal !== null) {
    plano_inicial.value = null
    plano_final.value = null
  }
})
</script>
