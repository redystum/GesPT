<template>
  <div>
    <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">
      {{ $t('wizard.plan_step.title') }}
    </h2>

    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">{{
          $t('wizard.plan_step.academic_year')
        }}</label>
        <input
          type="text"
          v-model="plan.ano"
          @input="validateYear"
          :placeholder="$t('wizard.plan_step.academic_year_placeholder')"
          class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
          :class="{ 'border-red-500 focus:ring-red-500/20': errorMsg }"
        />
        <p v-if="errorMsg" class="text-red-500 text-xs mt-1">{{ errorMsg }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useI18n } from '@/i18n'

const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])
const { t } = useI18n()

const plan = ref({ ano: '' })
const errorMsg = ref('')

const validateYear = () => {
  const regex = /^20\d{2}\/(20)?\d{2}$/
  if (!plan.value.ano) {
    errorMsg.value = ''
    return
  }

  if (!regex.test(plan.value.ano)) {
    errorMsg.value = t('wizard.plan_step.error_invalid_format')
    return
  }

  const parts = plan.value.ano.split('/')
  const year1 = parseInt(parts[0], 10)
  let year2 = parseInt(parts[1], 10)

  if (parts[1].length === 2) {
    year2 = 2000 + year2
  }

  if (year2 !== year1 + 1) {
    errorMsg.value = t('wizard.plan_step.error_invalid_sequence')
    return
  }

  errorMsg.value = ''
}

onMounted(() => {
  if (props.modelValue) {
    plan.value.ano = props.modelValue.ano || ''
    validateYear()
  }
})

watch(
  plan,
  () => {
    emit('update:modelValue', { ...plan.value, isValid: !errorMsg.value })
  },
  { deep: true }
)
</script>
