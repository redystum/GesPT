<template>
  <div class="relative">
    <input
      type="text"
      v-model="searchQuery"
      @focus="onFocus"
      @blur="onBlur"
      @keydown.down.prevent="highlightNext"
      @keydown.up.prevent="highlightPrev"
      @keydown.enter.prevent="selectHighlighted"
      @input="onInput"
      class="w-full rounded-md border p-2 bg-white dark:bg-[#1a1a1a] dark:text-white dark:border-neutral-700"
      :class="inputClass"
      :placeholder="placeholder"
    />
    <div v-show="showDropdown" ref="dropdownRef" class="absolute z-50 w-full mt-1 bg-white dark:bg-[#1a1a1a] border dark:border-neutral-700 rounded-md shadow-lg max-h-60 overflow-auto">
      <div
        v-for="(option, index) in filteredOptions"
        :key="index"
        @mousedown.prevent="selectOption(option)"
        @mouseover="highlightedIndex = index"
        :class="[
          'px-4 py-2 cursor-pointer text-left',
          highlightedIndex === index ? 'bg-neutral-200 dark:bg-neutral-700' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800'
        ]"
      >
        <slot name="option" :option="option">
          {{ displayFn(option) }}
        </slot>
      </div>
      <div v-if="filteredOptions.length === 0" class="px-4 py-2 text-neutral-500 text-sm text-left">
        <slot name="no-options">No results found.</slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'

const props = defineProps({
  modelValue: {},
  options: { type: Array, default: () => [] },
  displayFn: { type: Function, required: true },
  searchFn: { type: Function, default: null },
  placeholder: { type: String, default: 'Search...' },
  valueKey: { type: String, default: 'id' },
  inputClass: { type: String, default: '' },
  returnObject: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'change'])

const searchQuery = ref('')
const showDropdown = ref(false)
const highlightedIndex = ref(-1)
const dropdownRef = ref(null)

const normalizeString = (str) => str ? String(str).normalize('NFD').replace(/[\u0300-\u036f]/g, "") : ''

const isSelected = computed(() => props.modelValue !== null && props.modelValue !== undefined && props.modelValue !== '')

const updateSearchQueryFromValue = () => {
  if (!isSelected.value) {
    if (!showDropdown.value) searchQuery.value = ''
    return
  }
  const option = props.options.find(opt => {
    if (props.returnObject) {
      if (props.valueKey && opt[props.valueKey] && typeof props.modelValue === 'object' && props.modelValue[props.valueKey]) {
        return opt[props.valueKey] === props.modelValue[props.valueKey]
      }
      return opt === props.modelValue
    }
    const val = props.valueKey ? opt[props.valueKey] : opt
    return val === props.modelValue
  })
  if (option) {
    searchQuery.value = props.displayFn(option)
  }
}

watch(() => props.modelValue, updateSearchQueryFromValue, { immediate: true, deep: true })
watch(() => props.options, updateSearchQueryFromValue, { deep: true })

const filteredOptions = computed(() => {
  if (!searchQuery.value || (showDropdown.value === false && isSelected.value)) return props.options
  if (isSelected.value && !showDropdown.value) return props.options

  const query = normalizeString(searchQuery.value.toLowerCase())
  if (props.searchFn) {
    return props.options.filter(opt => props.searchFn(opt, query))
  }
  return props.options.filter(opt => {
    const text = props.displayFn(opt)
    return normalizeString(text.toLowerCase()).includes(query)
  })
})

const onInput = () => {
  emit('update:modelValue', props.returnObject ? null : '')
  emit('change', null)
  highlightedIndex.value = 0
  showDropdown.value = true
}

const onFocus = () => {
  showDropdown.value = true
  updateSearchQueryFromValue()
}

const onBlur = () => {
  setTimeout(() => {
    showDropdown.value = false
    highlightedIndex.value = -1
    if (!isSelected.value) {
      searchQuery.value = ''
    } else {
      updateSearchQueryFromValue()
    }
  }, 150)
}

const highlightNext = () => {
  if (!showDropdown.value) {
    showDropdown.value = true
    highlightedIndex.value = 0
    return
  }
  if (highlightedIndex.value < filteredOptions.value.length - 1) {
    highlightedIndex.value++
    scrollToHighlighted()
  }
}

const highlightPrev = () => {
  if (highlightedIndex.value > 0) {
    highlightedIndex.value--
    scrollToHighlighted()
  }
}

const selectHighlighted = () => {
  if (showDropdown.value && highlightedIndex.value >= 0 && highlightedIndex.value < filteredOptions.value.length) {
    selectOption(filteredOptions.value[highlightedIndex.value])
  } else if (!showDropdown.value) {
    showDropdown.value = true
  }
}

const scrollToHighlighted = async () => {
  await nextTick()
  if (dropdownRef.value) {
    const activeItem = dropdownRef.value.children[highlightedIndex.value]
    if (activeItem) {
      if (activeItem.offsetTop < dropdownRef.value.scrollTop) {
        dropdownRef.value.scrollTop = activeItem.offsetTop
      } else if (activeItem.offsetTop + activeItem.offsetHeight > dropdownRef.value.scrollTop + dropdownRef.value.clientHeight) {
        dropdownRef.value.scrollTop = activeItem.offsetTop + activeItem.offsetHeight - dropdownRef.value.clientHeight
      }
    }
  }
}

const selectOption = (opt) => {
  const val = props.returnObject ? opt : (props.valueKey ? opt[props.valueKey] : opt)
  emit('update:modelValue', val)
  emit('change', opt)
  searchQuery.value = props.displayFn(opt)
  showDropdown.value = false
  highlightedIndex.value = -1
}
</script>
