import { defineStore } from 'pinia'
import { ref } from 'vue'

export const usePlanWizardStore = defineStore('planWizard', () => {
  const wizardData = ref({
    course: { isNew: false, data: {} },
    plan: { ano: '' },
    ucs: [],
    equivalences: {
      prevPlanId: '',
      equivalences: []
    }
  })

  const resetWizard = () => {
    wizardData.value = {
      course: { isNew: false, data: {} },
      plan: { ano: '' },
      ucs: [],
      equivalences: {
        prevPlanId: '',
        equivalences: []
      }
    }
  }

  return { wizardData, resetWizard }
})
