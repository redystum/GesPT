<template>
  <div>
    <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-neutral-100">
      {{ $t('wizard.plan_step.title') }}
    </h2>

    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">{{
          $t('wizard.plan_step.academic_year')
        }} *</label>
        <input
          ref="yearInput"
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
const yearInput = ref(null)
let previousLength = 0

const validateYear = () => {
  const currentLength = plan.value.ano?.length || 0
  const isTyping = currentLength > previousLength
  previousLength = currentLength

  if (!plan.value.ano) {
    errorMsg.value = ''
    return
  }

  // Auto-complete if typing and matches exactly 4 digits like 2026
  if (isTyping && /^20\d{2}$/.test(plan.value.ano)) {
    const year1 = parseInt(plan.value.ano, 10)
    const year2 = (year1 + 1).toString().slice(-2)
    plan.value.ano = `${year1}/${year2}`
    previousLength = plan.value.ano.length

    // Remove focus from the input
    if (yearInput.value) {
      yearInput.value.blur()
    }
  }

  const regex = /^20\d{2}\/(20)?\d{2}$/

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
    previousLength = plan.value.ano.length
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