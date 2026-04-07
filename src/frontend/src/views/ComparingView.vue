<template>
  <div class="page-container page-section animate-fade-in-up">
    <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <p class="section-kicker">{{ $t('compare.kicker') }}</p>
        <h1 class="page-title mt-2">{{ $t('compare.title') }}</h1>
        <p class="page-subtitle">{{ $t('compare.subtitle') }}</p>
      </div>

      <section class="simple-card w-full p-5 lg:w-auto">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="min-w-56">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.12em] text-[color:var(--muted)]">
              {{ $t('compare.student') }}
            </label>
            <select v-model="estudante_id" class="simple-select">
              <option v-for="n in 3" :key="n" :value="n">{{ $t('compare.student') }} {{ n }}</option>
            </select>
          </div>

          <div class="min-w-56">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.12em] text-[color:var(--muted)]">
              {{ $t('compare.course') }}
            </label>
            <select v-model="curso_id" class="simple-select">
              <option v-for="c in cursos" :key="c.id" :value="c.id">
                {{ locale === 'pt' ? c.nome : c.name }}
              </option>
            </select>
          </div>
        </div>
      </section>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
      <article class="simple-card p-5">
        <p class="section-kicker">{{ $t('compare.no_antecedente') }}</p>
        <p class="mt-2 text-3xl font-bold">{{ statsNoAntecedente }}</p>
      </article>

      <article class="simple-card p-5">
        <p class="section-kicker">{{ $t('compare.descontinuadas') }}</p>
        <p class="mt-2 text-3xl font-bold">{{ statsDescontinuadas }}</p>
      </article>

      <article class="simple-card p-5">
        <p class="section-kicker">{{ $t('compare.equivalences') }}</p>
        <p class="mt-2 text-3xl font-bold">{{ statsEquivalences }}</p>
      </article>
    </div>

    <section v-if="isReady" class="compare-table">
      <div class="compare-table-header grid grid-cols-1 p-4 sm:grid-cols-2 sm:gap-4">
        <div class="space-y-1">
          <label class="section-kicker">{{ $t('compare.initial_plan') }}</label>
          <select v-model="plano_inicial" class="simple-select">
            <option v-for="p in planos_disponiveis" :key="p.id" :value="p.id">{{ p.ano }}</option>
          </select>
        </div>

        <div class="space-y-1">
          <label class="section-kicker">{{ $t('compare.target_plan') }}</label>
          <select v-model="plano_final" class="simple-select">
            <option v-for="p in planos_disponiveis" :key="p.id" :value="p.id">{{ p.ano }}</option>
          </select>
        </div>
      </div>

      <div v-if="equivalences.length === 0" class="state-box m-4">
        <div>
          <p class="font-semibold text-[color:var(--text)]">{{ $t('states.empty_title') }}</p>
          <p class="text-sm text-[color:var(--muted)]">{{ $t('compare.empty_hint') }}</p>
        </div>
      </div>

      <div
        v-for="(item, index) in equivalences"
        :key="index"
        class="compare-row grid grid-cols-1 sm:grid-cols-2"
      >
        <div class="border-b-2 border-[color:var(--border-strong)] sm:border-b-0 sm:border-r-2 sm:border-r-[color:var(--border-strong)]">
          <div
            v-for="(part, idx) in item.inicial"
            :key="part?.data ? part.data.id : 'no-inicial-' + index + '-' + idx"
            class="p-5"
            :class="dividerClass(item, 'inicial', idx, item.inicial.length)"
          >
            <template v-if="part.data">
              <p class="font-semibold">
                {{ locale === 'pt' ? part.data.uc.nome : part.data.uc.name }}
                <span
                  v-if="part.data.opcao_escolhida"
                  class="ml-2 text-sm font-medium text-[color:var(--primary)]"
                >
                  {{ locale === 'pt' ? part.data.opcao_escolhida.nome : part.data.opcao_escolhida.name }}
                </span>
              </p>
              <p class="mt-1 text-xs text-[color:var(--muted)]">{{ $t('compare.ects') }}: {{ part.data.uc.ects }}</p>
            </template>
            <p v-else class="text-sm italic text-[color:var(--muted)]">{{ $t('compare.no_inicial') }}</p>
          </div>
        </div>

        <div>
          <div
            v-for="(part, idx) in item.final"
            :key="part?.data ? part.data.id : 'no-final-' + index + '-' + idx"
            class="p-5"
            :class="dividerClass(item, 'final', idx, item.final.length)"
          >
            <template v-if="part.data">
              <p class="font-semibold">
                {{ locale === 'pt' ? part.data.uc.nome : part.data.uc.name }}
                <span
                  v-if="part.data.opcao_escolhida"
                  class="ml-2 text-sm font-medium text-[color:var(--primary)]"
                >
                  {{ locale === 'pt' ? part.data.opcao_escolhida.nome : part.data.opcao_escolhida.name }}
                </span>
              </p>
              <p class="mt-1 text-xs text-[color:var(--muted)]">{{ $t('compare.ects') }}: {{ part.data.uc.ects }}</p>
            </template>
            <p v-else class="text-sm italic text-[color:#b91c1c] dark:text-[color:#fca5a5]">{{ $t('compare.no_final') }}</p>
          </div>
        </div>
      </div>
    </section>

    <div v-else class="state-box">
      <div>
        <p class="font-semibold text-[color:var(--text)]">{{ $t('compare.select_prompt_title') }}</p>
        <p class="text-sm text-[color:var(--muted)]">{{ $t('compare.select_prompt_subtitle') }}</p>
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
    return 'border-b-2 border-dashed border-[color:var(--border-strong)]'
  }

  return 'border-b border-[color:var(--border-strong)]'
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
