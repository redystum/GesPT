<template>
  <div class="max-w-screen-2xl mx-auto px-6 py-10 animate-fade-in-up">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white mb-2 tracking-tight">
          Plan Comparison
        </h1>
        <p class="text-neutral-500 dark:text-neutral-400 text-sm">
          Technical cross-analysis: Legacy vs Proposed Structure
        </p>
      </div>
      <div
        class="flex items-center gap-4 bg-white dark:bg-neutral-900 p-4 rounded-lg shadow-sm border border-neutral-200 dark:border-neutral-800"
      >
        <label class="text-xs text-neutral-500">Estudante:</label>
        <select
          v-model="estudante_id"
          class="text-sm border border-neutral-300 dark:border-neutral-700 bg-transparent rounded p-1 dark:text-white"
        >
          <option v-for="n in 3" :key="n" :value="n">{{ n }}</option>
        </select>

        <label class="text-xs text-neutral-500">Inicial:</label>
        <select
          v-model="plano_inicial"
          class="text-sm border border-neutral-300 dark:border-neutral-700 bg-transparent rounded p-1 dark:text-white"
        >
          <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
        </select>

        <label class="text-xs text-neutral-500">Final:</label>
        <select
          v-model="plano_final"
          class="text-sm border border-neutral-300 dark:border-neutral-700 bg-transparent rounded p-1 dark:text-white"
        >
          <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
        </select>

        <button
          class="text-xs border border-neutral-700 rounded px-2 py-1 dark:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800"
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
    <div class="simple-card overflow-hidden">
      <div
        class="grid grid-cols-2 text-xs font-semibold text-neutral-500 dark:text-neutral-400 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50"
      >
        <div
          class="p-4 border-r border-neutral-200 dark:border-neutral-800 flex justify-between items-center"
        >
          <span>Plano Inicial</span>
        </div>
        <div class="p-4 flex justify-between items-center">
          <span class="text-neutral-900 dark:text-white">Plano Final</span>
        </div>
      </div>

      <!-- Item from equivalences -->
      <div
        v-for="(item, index) in equivalences"
        :key="index"
        class="grid grid-cols-2 border-b border-neutral-100 dark:border-neutral-800/50"
      >
<!--        <div>-->
<!--          {{item}}-->
<!--        </div>-->
        <div
          v-for="part in item.inicial"
          :key="part?.data ? part.data.id : 'no-inicial-' + index"
          :style="`column-span: ${item.final?.length};`"
          class="p-5 border-r border-neutral-100 dark:border-neutral-800/50 flex flex-col justify-center"
        >
<!--          {{part}}-->
            <p v-if="part.data" class="font-medium text-neutral-900 dark:text-white mb-1">
              {{ part.data.uc.nome }}
            </p>
            <p v-else class="font-medium text-neutral-900 dark:text-white mb-1">
              {{ part.text }}
            </p>
        </div>
        <div
          v-for="part in item.final"
          :key="part?.data ? part.data.id : 'no-inicial-' + index"
          :style="`column-span: ${item.initial?.length};`"
          class="p-5 flex justify-between items-center"
        >
            <p v-if="part.data" class="font-medium text-neutral-900 dark:text-white mb-1">
              {{ part.data.uc.nome }}
            </p>
            <p v-else class="font-medium text-neutral-900 dark:text-white mb-1">
              {{ part.text }}
            </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, inject } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
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
    return {
      inicial: format_part(item, 'inicial'),
      final: format_part(item, 'final')
    }
  })
}

function format_part(item, side) {
  if (side !== 'inicial' && side !== 'final') {
    throw new Error('Invalid side')
  }

  if (!item[side]) {
    return [{
      text: t('compare.no_' + side),
      data: null
    }]
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
