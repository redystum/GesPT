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
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="min-w-56">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.12em]">
              {{ $t('compare.student') }}
            </label>
            <select
              v-model="estudante_id"
              class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2.5 text-sm outline-none transition-all duration-150 bg-white dark:bg-[#1a1a1a] text-neutral-900 dark:text-neutral-100 focus:border-neutral-500 focus:ring-4 focus:ring-neutral-500/20"
            >
              <option v-for="n in 3" :key="n" :value="n">
                {{ $t('compare.student') }} {{ n }}
              </option>
            </select>
          </div>

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
            <option v-for="p in planos_disponiveis" :key="p.id" :value="p.id">{{ p.ano }}</option>
          </select>
        </div>
      </div>

      <div
        v-if="equivalences.length === 0"
        class="flex items-start gap-3 rounded-md border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50 px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 m-4"
      >
        <div>
          <p class="font-semibold">{{ $t('states.empty_title') }}</p>
          <p class="text-sm">{{ $t('compare.empty_hint') }}</p>
        </div>
      </div>

      <div
        v-for="(item, index) in equivalences"
        :key="index"
        class="border-b-2 border-neutral-500 dark:border-neutral-600 grid grid-cols-1 sm:grid-cols-2"
      >
        <div class="border-b-0 border-r-2 border-r-neutral-500 flex flex-col justify-center">
          <div
            v-for="(part, idx) in item.inicial"
            :key="part?.data ? part.data.id : 'no-inicial-' + index + '-' + idx"
            class="p-5 border-b-neutral-400 dark:border-b-neutral-600"
            :class="dividerClass(item, 'inicial', idx, item.inicial.length)"
          >
            <template v-if="part.data">
              <p class="font-semibold">
                {{ part.data.uc.nome }}
                <span v-if="part.data.opcao_escolhida" class="font-medium">
                  <font-awesome-icon :icon="['fas', 'arrow-right']" class="mr-1" />
                  {{ part.data.opcao_escolhida.nome }}
                </span>
              </p>
              <p class="mt-1 text-xs">{{ $t('compare.ects') }}: {{ part.data.uc.ects }}</p>
            </template>
            <p v-else class="text-sm italic">{{ $t('compare.no_inicial') }}</p>
          </div>
        </div>

        <div class="flex flex-col justify-center">
          <div
            v-for="(part, idx) in item.final"
            :key="part?.data ? part.data.id : 'no-final-' + index + '-' + idx"
            class="p-5 border-neutral-300"
            :class="dividerClass(item, 'final', idx, item.final.length)"
          >
            <template v-if="part.data">
              <p class="font-semibold">
                {{ part.data.uc.nome }}
                <span v-if="part.data.opcao_escolhida" class="font-medium">
                  <font-awesome-icon :icon="['fas', 'arrow-right']" class="mr-1" />
                  {{ part.data.opcao_escolhida.nome }}
                </span>
              </p>
              <p class="mt-1 text-xs">{{ $t('compare.ects') }}: {{ part.data.uc.ects }}</p>
            </template>
            <p v-else class="text-sm italic text-[#b91c1c] dark:text-[#fca5a5]">
              {{ $t('compare.no_final') }}
            </p>
          </div>
        </div>
      </div>
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
import { ref, onMounted, watch, inject, computed } from 'vue'
import { useI18n } from '@/i18n'
import { useRoute } from 'vue-router'

const { locale } = useI18n()
const route = useRoute()
const API = inject('apiURL')

const estudante_id = ref(1)
const curso_id = ref(null)
const plano_inicial = ref(null)
const plano_final = ref(null)
const data = ref([])
const cursos = ref([])
const equivalences = ref([])

const isReady = computed(() => Boolean(curso_id.value && plano_inicial.value && plano_final.value))

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
    const response = await fetch(`${API}/cursos?with_planos_estudo=1`)
    const res = await response.json()
    cursos.value = res.data

    if (cursos.value.length > 0) {
      if (route.query.course && cursos.value.some((c) => c.id == route.query.course)) {
        curso_id.value = Number(route.query.course)
      }

      if (planos_disponiveis.value.length > 0 && curso_id.value) {
        if (route.query.plan && planos_disponiveis.value.some((p) => p.id == route.query.plan)) {
          plano_inicial.value = Number(route.query.plan)
        }

        if (plano_inicial.value && planos_disponiveis.value.length > 1) {
          const outroPlano = planos_disponiveis.value.find((p) => p.id !== plano_inicial.value)
          plano_final.value = outroPlano ? outroPlano.id : null
        }
      }
    }
  } catch (error) {
    console.error(error)
  }
}

async function fetchData() {
  if (!plano_inicial.value || !plano_final.value) return

  const url = `${API}/compare?estudante_id=${estudante_id.value}&plano_inicial=${plano_inicial.value}&plano_final=${plano_final.value}`

  try {
    const response = await fetch(url)
    const payload = await response.json()
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

function mappingCount(item, side) {
  return item[side].filter((part) => part.data).length
}

function shouldUseDashedDivider(item, side) {
  const otherSide = side === 'inicial' ? 'final' : 'inicial'
  return mappingCount(item, side) > 1 && mappingCount(item, otherSide) === 1
}

function dividerClass(item, side, idx, listLength) {
  if (idx >= listLength - 1) {
    return ''
  }

  if (shouldUseDashedDivider(item, side)) {
    return 'border-b-2 border-dashed'
  }

  return 'border-b'
}

onMounted(async () => {
  await fetchCursos()
  await fetchData()
})

watch([estudante_id, plano_inicial, plano_final], fetchData)
watch(curso_id, () => {
  if (planos_disponiveis.value.length > 0) {
    plano_inicial.value = planos_disponiveis.value[0].id
    plano_final.value =
      planos_disponiveis.value.length > 1
        ? planos_disponiveis.value[1].id
        : planos_disponiveis.value[0].id
  }
})
</script>
