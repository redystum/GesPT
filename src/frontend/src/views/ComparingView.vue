<template>
  <div class="max-w-screen-2xl mx-auto px-6 py-10 animate-fade-in-up">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
          Plan Comparison
        </h1>
        <p class="text-neutral-500 dark:text-neutral-400">
          Technical cross-analysis: Legacy vs Proposed Structure
        </p>
      </div>
      <div
        class="flex flex-wrap items-center gap-4 bg-white dark:bg-neutral-900 p-4 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-800"
      >
        <div class="flex items-center gap-2">
          <label class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Estudante:</label>
          <select
            v-model="estudante_id"
            class="text-sm border border-neutral-300 dark:border-neutral-700 bg-transparent rounded p-1 dark:text-white"
          >
            <option v-for="n in 3" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <div class="flex items-center gap-2">
          <label class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Inicial:</label>
          <select
            v-model="plano_inicial"
            class="text-sm border border-neutral-300 dark:border-neutral-700 bg-transparent rounded p-1 dark:text-white"
          >
            <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <div class="flex items-center gap-2">
          <label class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Final:</label>
          <select
            v-model="plano_final"
            class="text-sm border border-neutral-300 dark:border-neutral-700 bg-transparent rounded p-1 dark:text-white"
          >
            <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <button
          class="text-xs border border-neutral-700 rounded px-3 py-1.5 dark:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 font-medium transition-colors"
          @click="
            () => {
              estudante_id = 3
              plano_inicial = 3
              plano_final = 4
            }
          "
        >
          See Example
        </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1">
            Total Variance
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">0 ECTS</p>
        </div>
      </div>
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1">
            Equivalence Rate
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">84%</p>
        </div>
      </div>
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1">
            New Modules
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">12</p>
        </div>
      </div>
    </div>

    <!-- Comparison Table -->
    <div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl overflow-hidden">
      <div
        class="grid grid-cols-2 text-xs font-bold text-neutral-500 uppercase tracking-wider border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50"
      >
        <div class="p-4 border-r border-neutral-200 dark:border-neutral-800 flex justify-between items-center">
          <span>Plano Inicial</span><span class="text-neutral-400">Legacy</span>
        </div>
        <div class="p-4 flex justify-between items-center">
          <span>Plano Final</span><span class="text-teal-600 dark:text-teal-400">Proposed</span>
        </div>
      </div>

      <!-- Item from equivalences -->
      <div
        v-for="(item, index) in equivalences"
        :key="index"
        class="grid grid-cols-2 border-b border-neutral-200 dark:border-neutral-800"
      >
        <div class="flex flex-col border-r border-neutral-200 dark:border-neutral-800">
          <div
            v-for="(part, idx) in item.inicial"
            :key="part?.data ? part.data.id : 'no-inicial-' + index + '-' + idx"
            class="p-6 flex-1 flex flex-col justify-center hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition-colors"
            :class="{
              'border-b border-neutral-100 dark:border-neutral-800': idx < item.inicial.length - 1,
              'opacity-50 bg-neutral-50 dark:bg-neutral-900/50': !part.data
            }"
          >
            <div v-if="part.data">
              <p class="font-bold text-neutral-900 dark:text-white flex items-center flex-wrap gap-2">
                <span>{{ locale === 'pt' ? part.data.uc.nome : part.data.uc.name }}</span>
                <span v-if="part.data.opcao_escolhida" class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded text-xs font-semibold">
                  {{ locale === 'pt' ? part.data.opcao_escolhida.nome : part.data.opcao_escolhida.name }}
                </span>
              </p>
              <p class="text-xs text-neutral-500 mt-1">ECTS: {{ part.data.uc.ects }}</p>
            </div>
            <div v-else class="flex items-center">
              <span class="text-xs italic text-neutral-400">{{ part.text }}</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col">
          <div
            v-for="(part, idx) in item.final"
            :key="part?.data ? part.data.id : 'no-final-' + index + '-' + idx"
            class="p-6 flex-1 flex justify-between items-start hover:bg-neutral-50 dark:hover:bg-neutral-800/30 transition-colors"
            :class="{
              'border-b border-neutral-100 dark:border-neutral-800': idx < item.final.length - 1,
              'bg-red-50/30 dark:bg-red-900/10 items-center': !part.data
            }"
          >
            <div v-if="part.data">
              <p class="font-bold text-neutral-900 dark:text-white flex items-center flex-wrap gap-2">
                <span>{{ locale === 'pt' ? part.data.uc.nome : part.data.uc.name }}</span>
                <span v-if="part.data.opcao_escolhida" class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded text-xs font-semibold">
                  {{ locale === 'pt' ? part.data.opcao_escolhida.nome : part.data.opcao_escolhida.name }}
                </span>
              </p>
              <p class="text-xs text-neutral-500 mt-1">ECTS: {{ part.data.uc.ects }}</p>
            </div>
            <div v-else class="flex items-center w-full">
              <span class="text-[10px] font-bold text-red-700 dark:text-red-400 uppercase">{{ part.text }}</span>
            </div>

            <span v-if="part.data && !item.inicial.some(i => i.data)" class="bg-teal-800 text-white px-2 py-1 text-[10px] font-bold rounded self-start ml-2">NEW</span>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6 flex gap-6 text-xs font-bold text-neutral-500 uppercase">
      <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-teal-600"></span> Equivalence</div>
      <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-600"></span> Discontinued</div>
      <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-slate-800 dark:bg-neutral-400"></span> Structural Shift</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, inject } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()
const API = inject('apiURL')

const estudante_id = ref(1)
const plano_inicial = ref(1)
const plano_final = ref(2)
const data = ref([])
const equivalences = ref([])

async function fetchData() {
  const url = `${API}/compare?estudante_id=${estudante_id.value}&plano_inicial=${plano_inicial.value}&plano_final=${plano_final.value}`
  try {
    const response = await fetch(url)
    let data_ = await response.json()
    data.value = data_.data
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
        text: t('compare.no_' + side),
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
        text: t('compare.no_' + side),
        data: null
      })
    }
  }

  return result
}

onMounted(fetchData)
watch([estudante_id, plano_inicial, plano_final], fetchData)
</script>
