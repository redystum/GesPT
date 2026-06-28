<script setup>
import SearchableDropdown from '@/components/SearchableDropdown.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

defineProps({
  equivalences: {},
  plano_final: {}
})
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
</script>

<template>
  <div
    v-for="(item, index) in equivalences"
    :key="index"
    class="border-b-2 border-neutral-500 dark:border-neutral-600 grid grid-cols-2"
  >
    <div class="border-b-0 border-r-2 border-r-neutral-500 flex flex-col justify-center">
      <div
        v-for="(part, idx) in item.inicial"
        :key="part?.data ? part.data.id : 'no-inicial-' + index + '-' + idx"
        class="p-2 sm:p-5 border-b-neutral-400 dark:border-b-neutral-600"
        :class="dividerClass(item, 'inicial', idx, item.inicial.length)"
      >
        <template v-if="part.data">
          <div class="flex justify-between">
            <div class="mr-2 sm:mr-12 wrap-break-word sm:text-nowrap text-sm sm:text-base">
              <p class="font-semibold flex flex-wrap items-center gap-1.5">
                <router-link
                  v-if="authStore.hasPermission('ucs.view')"
                  :to="{ name: 'uc-detail', params: { id: part.data.uc.id } }"
                  class="hover:underline hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer"
                >
                  {{ part.data.uc.nome }}
                </router-link>
                <span v-else>{{ part.data.uc.nome }}</span>
                <span v-if="part.data.opcoes && part.data.opcoes.length > 0" class="inline-flex items-center gap-1.5 font-medium min-w-[200px] text-xs">
                  <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-neutral-500" />
                  <SearchableDropdown
                    v-model="part.data.opcao_escolhida"
                    :options="part.data.opcoes.map(o => o.uc_escolhida).filter(Boolean)"
                    :displayFn="opt => opt.nome"
                    :returnObject="true"
                    valueKey="id"
                    placeholder="Select option..."
                    inputClass="!py-1 !px-2 text-xs min-h-[30px]"
                  />
                </span>
                <span v-else-if="part.data.opcao_escolhida" class="font-medium">
                  <font-awesome-icon :icon="['fas', 'arrow-right']" class="mr-1" />
                  <router-link
                    v-if="authStore.hasPermission('ucs.view')"
                    :to="{ name: 'uc-detail', params: { id: part.data.opcao_escolhida.id } }"
                    class="hover:underline hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer"
                  >
                    {{ part.data.opcao_escolhida.nome }}
                  </router-link>
                  <span v-else>{{ part.data.opcao_escolhida.nome }}</span>
                </span>
              </p>
              <div class="flex flex-wrap sm:flex-nowrap">
                <p class="mt-1 text-xs">{{ part.data.uc.sigla }}</p>
                <p class="mt-1 mx-2 px-2 border-x border-x-neutral-600 text-xs">
                  {{ $t('compare.ects') }}: {{ part.data.uc.ects }}
                </p>
                <p class="mt-1 text-xs">
                  {{ $t('compare.semester') }}: {{ part.data.uc.semestre }}
                </p>
              </div>
            </div>
            <div class="hidden sm:block">
              <p
                v-if="part.data.observacao"
                class="mt-1 text-xs italic text-neutral-500 dark:text-neutral-400 text-right"
              >
                {{ part.data.observacao }}
              </p>
            </div>
          </div>
        </template>
        <p v-else class="text-sm italic">{{ $t('compare.no_inicial') }}</p>
      </div>
    </div>

    <div class="flex flex-col justify-center">
      <div
        v-for="(part, idx) in item.final"
        :key="part?.data ? part.data.id : 'no-final-' + index + '-' + idx"
        class="p-2 sm:p-5 border-neutral-300"
        :class="dividerClass(item, 'final', idx, item.final.length)"
      >
        <template v-if="part?.data">
          <div class="flex justify-between">
            <div class="mr-2 sm:mr-12 wrap-break-word sm:text-nowrap text-sm sm:text-base">
              <p class="font-semibold flex flex-wrap items-center gap-1.5">
                <router-link
                  v-if="authStore.hasPermission('ucs.view')"
                  :to="{ name: 'uc-detail', params: { id: part.data.uc.id } }"
                  class="hover:underline hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer"
                >
                  {{ part.data.uc.nome }}
                </router-link>
                <span v-else>{{ part.data.uc.nome }}</span>
                <span v-if="part.data.opcoes && part.data.opcoes.length > 0" class="inline-flex items-center gap-1.5 font-medium min-w-[200px] text-xs">
                  <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-neutral-500" />
                  <SearchableDropdown
                    v-model="part.data.opcao_escolhida"
                    :options="part.data.opcoes.map(o => o.uc_escolhida).filter(Boolean)"
                    :displayFn="opt => opt.nome"
                    :returnObject="true"
                    valueKey="id"
                    placeholder="Select option..."
                    inputClass="!py-1 !px-2 text-xs min-h-[30px]"
                  />
                </span>
                <span v-else-if="part.data.opcao_escolhida" class="font-medium">
                  <font-awesome-icon :icon="['fas', 'arrow-right']" class="mr-1" />
                  <router-link
                    v-if="authStore.hasPermission('ucs.view')"
                    :to="{ name: 'uc-detail', params: { id: part.data.opcao_escolhida.id } }"
                    class="hover:underline hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer"
                  >
                    {{ part.data.opcao_escolhida.nome }}
                  </router-link>
                  <span v-else>{{ part.data.opcao_escolhida.nome }}</span>
                </span>
              </p>
              <div class="flex flex-wrap sm:flex-nowrap">
                <p class="mt-1 text-xs">{{ part.data.uc.sigla }}</p>
                <p class="mt-1 mx-2 px-2 border-x border-x-neutral-600 text-xs">
                  {{ $t('compare.ects') }}: {{ part.data.uc.ects }}
                </p>
                <p class="mt-1 text-xs">
                  {{ $t('compare.semester') }}: {{ part.data.uc.semestre }}
                </p>
              </div>
            </div>
            <div class="hidden sm:block">
              <p
                v-if="part.data.observacao"
                class="mt-1 text-xs italic text-neutral-500 dark:text-neutral-400 text-right"
              >
                {{ part.data.observacao }}
              </p>
            </div>
          </div>
        </template>
        <p v-else-if="plano_final" class="text-sm italic text-[#b91c1c] dark:text-[#fca5a5]">
          {{ $t('compare.no_final') }}
        </p>
      </div>
    </div>
  </div>
</template>