<template>
  <div class="max-w-screen-2xl mx-auto px-6 py-10 animate-fade-in-up">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
          {{ $t('compare.title') }}
        </h1>
        <p class="text-neutral-500 dark:text-neutral-400">
          {{ $t('compare.subtitle') }}
        </p>
      </div>

      <div
        class="flex flex-wrap items-center gap-4 bg-white dark:bg-neutral-900 p-4 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800"
      >
        <div class="flex flex-col min-w-37.5">
          <label class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-1 block">{{
            $t('compare.student')
          }}</label>
          <select
            v-model="estudante_id"
            class="w-full text-sm border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 rounded-lg px-3 py-2 text-neutral-900 dark:text-white outline-none transition-all shadow-sm cursor-pointer"
          >
            <option v-for="n in 3" :key="n" :value="n">Estudante {{ n }}</option>
          </select>
        </div>

        <div class="flex flex-col min-w-50">
          <label class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-1 block">{{
            $t('compare.course')
          }}</label>
          <select
            v-model="curso_id"
            class="w-full text-sm border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 rounded-lg px-3 py-2 text-neutral-900 dark:text-white outline-none transition-all shadow-sm cursor-pointer"
          >
            <option v-for="c in cursos" :key="c.id" :value="c.id">
              {{ locale === 'pt' ? c.nome : c.name }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1">
            {{ $t('compare.no_antecedente') }}
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">
            {{ statsNoAntecedente }}
          </p>
        </div>
      </div>
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1">
            {{ $t('compare.descontinuadas') }}
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">
            {{ statsDescontinuadas }}
          </p>
        </div>
      </div>
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider mb-1">
            {{ $t('compare.equivalences') }}
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ statsEquivalences }}</p>
        </div>
      </div>
    </div>

    <!-- Comparison Table -->
    <div
      v-if="curso_id && plano_inicial && plano_final"
      class="bg-white dark:bg-neutral-900 border border-neutral-400 dark:border-neutral-700 rounded-xl overflow-hidden"
    >
      <div
        class="grid grid-cols-2 text-xs font-bold text-white uppercase tracking-wider border-b-2 border-neutral-400 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50"
      >
        <div
          class="p-4 border-r-2 border-neutral-400 dark:border-neutral-700 flex flex-wrap gap-2 justify-between items-center text-neutral-900 dark:text-white"
        >
          <div class="flex items-center gap-3 w-full sm:w-auto">
            <select
              v-model="plano_inicial"
              class="text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-lg px-3 py-1.5 text-neutral-900 dark:text-white outline-none shadow-sm cursor-pointer pr-8 bg-size-[12px_12px] bg-position-[right_10px_center] bg-no-repeat"
            >
              <option v-for="p in planos_disponiveis" :key="p.id" :value="p.id">{{ p.ano }}</option>
            </select>
          </div>
        </div>
        <div
          class="p-4 flex flex-wrap gap-2 justify-between items-center text-neutral-900 dark:text-white"
        >
          <div class="flex items-center gap-3 w-full sm:w-auto">
            <select
              v-model="plano_final"
              class="text-sm border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 rounded-lg px-3 py-1.5 text-neutral-900 dark:text-white outline-none shadow-sm cursor-pointer pr-8 bg-size-[12px_12px] bg-position-[right_10px_center] bg-no-repeat"
            >
              <option v-for="p in planos_disponiveis" :key="p.id" :value="p.id">{{ p.ano }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Item from equivalences -->
      <div
        v-for="(item, index) in equivalences"
        :key="index"
        class="grid grid-cols-2 border-b-2 border-neutral-400 dark:border-neutral-700"
      >
        <div class="flex flex-col border-r-2 border-neutral-400 dark:border-neutral-700">
          <div
            v-for="(part, idx) in item.inicial"
            :key="part?.data ? part.data.id : 'no-inicial-' + index + '-' + idx"
            class="p-6 flex-1 flex flex-col justify-center hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition-colors"
            :class="{
              'border-b-2 border-neutral-200 dark:border-neutral-800':
                idx < item.inicial.length - 1,
              'bg-neutral-400/30 dark:bg-neutral-800/60': !part.data
            }"
          >
            <div v-if="part.data">
              <p
                class="font-bold text-neutral-900 dark:text-white flex items-center flex-wrap gap-2"
              >
                <span>{{ locale === 'pt' ? part.data.uc.nome : part.data.uc.name }}</span>
                <span
                  v-if="part.data.opcao_escolhida"
                  class="text-indigo-600 dark:text-indigo-400 text-sm font-medium"
                >
                  -
                  {{
                    locale === 'pt'
                      ? part.data.opcao_escolhida.nome
                      : part.data.opcao_escolhida.name
                  }}
                </span>
              </p>
              <p class="text-xs text-neutral-500 mt-1">ECTS: {{ part.data.uc.ects }}</p>
            </div>
            <div v-else class="flex items-center">
              <span class="text-sm italic text-black dark:text-neutral-400">{{
                $t('compare.no_inicial')
              }}</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col">
          <div
            v-for="(part, idx) in item.final"
            :key="part?.data ? part.data.id : 'no-final-' + index + '-' + idx"
            class="p-6 flex-1 flex justify-between items-center hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition-colors"
            :class="{
              'border-b-2 border-neutral-200 dark:border-neutral-700': idx < item.final.length - 1,
              'bg-red-300/40 dark:bg-red-900/10 items-center!': !part.data
            }"
          >
            <div v-if="part.data">
              <p
                class="font-bold text-neutral-900 dark:text-white flex items-center flex-wrap gap-2"
              >
                <span>{{ locale === 'pt' ? part.data.uc.nome : part.data.uc.name }}</span>
                <span
                  v-if="part.data.opcao_escolhida"
                  class="text-indigo-600 dark:text-indigo-400 text-sm font-medium"
                >
                  -
                  {{
                    locale === 'pt'
                      ? part.data.opcao_escolhida.nome
                      : part.data.opcao_escolhida.name
                  }}
                </span>
              </p>
              <p class="text-xs text-neutral-500 mt-1">ECTS: {{ part.data.uc.ects }}</p>
            </div>
            <div v-else class="flex items-center">
              <span class="text-sm italic text-red-700 dark:text-red-400">{{
                $t('compare.no_final')
              }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, inject, computed } from 'vue'
import { useI18n } from 'vue-i18n'
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
const initial_plan_date = ref('')
const final_plan_date = ref('')

const statsNoAntecedente = computed(() => {
  return equivalences.value.filter((item) => !item.inicial.some((p) => p.data)).length
})

const statsDescontinuadas = computed(() => {
  return equivalences.value.filter((item) => !item.final.some((p) => p.data)).length
})

// how many initials with more than 1 item
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
      } else {
        curso_id.value = null
      }

      if (planos_disponiveis.value.length > 0 && curso_id.value) {
        if (route.query.plan && planos_disponiveis.value.some((p) => p.id == route.query.plan)) {
          plano_inicial.value = Number(route.query.plan)
        } else {
          plano_inicial.value = null
        }

        if (plano_inicial.value && planos_disponiveis.value.length > 1) {
          const outro_plano = planos_disponiveis.value.find((p) => p.id !== plano_inicial.value)
          plano_final.value = outro_plano ? outro_plano.id : null
        } else {
          plano_final.value = null
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
    let data_ = await response.json()
    data.value = data_.data
    initial_plan_date.value = data_.plans.initial.ano
    final_plan_date.value = data_.plans.final.ano
    formatData()
  } catch (error) {
    console.error(error)
  }
}

function formatData() {
  equivalences.value = data.value.map((item) => {
    let data = {
      inicial: format_part(item, 'inicial'),
      final: format_part(item, 'final')
    }

    if (!data.inicial.data) {
      data.final.no_inicial = true
    }

    if (!data.final.data) {
      data.inicial.no_final = true
    }

    return data
  })
}

function format_part(item, side) {
  if (side !== 'inicial' && side !== 'final') {
    throw new Error('Invalid side')
  }

  if (!item[side]) {
    return [
      {
        data: null
      }
    ]
  }

  let result = []

  for (const inicial of item[side]) {
    if (inicial['uc']) {
      result.push({
        data: inicial
      })
    } else {
      result.push({
        data: null
      })
    }
  }

  return result
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
