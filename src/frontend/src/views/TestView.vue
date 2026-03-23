<script setup>
import { ref, onMounted, watch } from 'vue'

const estudante_id = ref(1)
const plano_inicial = ref(1)
const plano_final = ref(2)
const equivalences = ref([])

async function fetchData() {
  const url = `http://localhost:8000/api/home?estudante_id=${estudante_id.value}&plano_inicial=${plano_inicial.value}&plano_final=${plano_final.value}`
  try {
    const response = await fetch(url)
    let data = await response.json()
    equivalences.value = data.equivalences
    console.log(equivalences)
  } catch (error) {
    console.error(error)
  }
}

onMounted(fetchData)
watch([estudante_id, plano_inicial, plano_final], fetchData)
</script>

<template>
  <div>
    <label>Estudante ID:</label>
    <select v-model="estudante_id">
      <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
    </select>

    <label>Plano Inicial:</label>
    <select v-model="plano_inicial">
      <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
    </select>

    <label>Plano Final:</label>
    <select v-model="plano_final">
      <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
    </select>

    <table class="min-w-full table-auto border-collapse border dark:border-neutral-700">
      <thead class="bg-neutral-100 dark:bg-neutral-800">
        <tr>
          <th
            class="border border-neutral-300 dark:border-neutral-700 px-4 py-2 text-left dark:text-white"
          >
            Inicial
          </th>
          <th
            class="border border-neutral-300 dark:border-neutral-700 px-4 py-2 text-left dark:text-white"
          >
            Final
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(item, index) in equivalences"
          :key="index"
          :class="
            index % 2 === 0
              ? 'bg-white dark:bg-neutral-900 hover:bg-neutral-50 dark:hover:bg-neutral-700'
              : 'bg-neutral-50 dark:bg-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700'
          "
        >
          <td class="border border-neutral-300 dark:border-neutral-700 px-4 py-2 dark:text-white">
            {{
              item.inicial?.map((item) => item.uc?.nome || item.nome).join(', ') ?? 'N/A'
            }}
          </td>
          <td class="border border-neutral-300 dark:border-neutral-700 px-4 py-2 dark:text-white">
            <!--            {{-->
            <!--              item.final?.length == 0-->
            <!--                ? 'N/A'-->
            <!--                : (item.final?.map((item) => item.uc?.nome || item.nome).join(', ') ?? 'N/A')-->
            <!--            }}-->
            {{ item.uc?.nome ?? 'N/A' }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped></style>