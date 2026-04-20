import { defineStore } from 'pinia'
import { inject, ref, watch } from 'vue'

export const usePlanWizardStore = defineStore('planWizard', () => {
  const api = inject('api')

  const wizardData = ref({
    course: { isNew: false, data: {} },
    plan: { ano: '' },
    ucs: [],
    equivalences: {
      prevPlanId: '',
      equivalences: []
    }
  })

  const prevPlanData = ref(null)
  const prevPlans = ref([])
  const prevUcs = ref([])
  const prevUcsBySemester = ref({})
  const planPreview = ref([])
  const loadingPlan = ref(false)

  // [11:55] ca vamos nos implementar esta comparação de novo :/
  // [12:35] afinal é mais facil aqui :))
  // [2:15] retiro o que disse, n é assim tão mais facil
  // um dia a AI saberá fazer isto em 20s mas por enquanto são 2h...
  watch(
    [wizardData, prevPlanData],
    async () => {
      await fetchPrevPlanUcs(wizardData.value.equivalences.prevPlanId)

      if (prevUcs.value.length === 0 || wizardData.value.ucs.length === 0) {
        planPreview.value = []
        return
      }

      let already = []
      let preview = wizardData.value.ucs.map((uc) => {
        const match = prevUcs.value.find((puc) => puc.id === uc.id)
        let initial = []

        if (match) {
          already.push(uc.id)
          initial.push({
            data: {
              uc: match
            }
          })
        }

        const ucIdentifier = uc.id || uc.sigla || uc.nome || ''; // should hit id everytime or the backend may not like-it

        if (wizardData.value.equivalences.equivalences.some((eq) => eq.newUc === ucIdentifier)) {
          let eqs = wizardData.value.equivalences.equivalences.filter((eq) => eq.newUc === ucIdentifier)
          eqs.forEach((eq) => {
            prevUcs.value.forEach((puc) => {
              if (eq.oldUcs.includes(puc.id)) {
                already.push(puc.id)
                initial.push({
                  data: {
                    uc: puc
                  }
                })
              }
            })
          })
        }

        return {
          inicial: initial.length > 0 ? initial : [{ data: null }],
          final: [
            {
              data: {
                uc: uc
              }
            }
          ]
        }
      })

      prevUcs.value.map((puc) => {
        if (already.includes(puc.id)) {
          return
        }

        preview.push({
          inicial: [
            {
              data: { uc: puc }
            }
          ],
          final: [
            {
              data: null
            }
          ]
        })
      })

      planPreview.value = preview
      console.log('Updated plan preview based on wizard data:', planPreview.value, prevUcs.value)
    },
    { deep: true }
  )

  const mapPlanUcs = (plan) => {
    if (!plan) return []
    const list = plan.planoUcs || plan.ucs || []
    return list.map((pu) => pu.uc || pu)
  }

  const fetchLatestPlan = async (courseId) => {
    if (!courseId) return
    if (!api) return

    loadingPlan.value = true
    try {
      const response = await api.get(`/cursos/${courseId}/latest-plan`)
      const data = response.data
      prevPlanData.value = data.data || data
      wizardData.value.equivalences.prevPlanId = prevPlanData.value.id
    } catch (err) {
      if (err.response?.status === 404) {
        prevPlanData.value = null
        wizardData.value.equivalences.prevPlanId = ''
        return
      }
      console.error('Error fetching latest plan:', err)
    } finally {
      loadingPlan.value = false
    }
  }

  const fetchPrevPlans = async (courseId) => {
    if (!courseId) return
    if (!api) return
    try {
      const response = await api.get('/planos-estudo', {
        params: {
          curso_id: courseId
        }
      })
      const data = response.data
      prevPlans.value = data.data || data
    } catch (err) {
      console.error('Error fetching plans:', err)
    }
  }

  const fetchPrevPlanUcs = async (planId, semester = null) => {
    if (!planId) {
      prevUcs.value = []
      return []
    }
    if (!api) return []

    try {
      const response = await api.get(`/planos-estudo/${planId}/ucs`, {
        params: semester ? { semestre: semester } : undefined
      })
      const data = response.data
      const plan = data.data || data
      const mappedUcs = mapPlanUcs(plan)

      if (!semester) {
        prevUcs.value = mappedUcs
        return mappedUcs
      }

      const semesterFiltered = mappedUcs.filter((uc) => Number(uc.semestre) === Number(semester))
      prevUcsBySemester.value = {
        ...prevUcsBySemester.value,
        [semester]: semesterFiltered
      }
      return semesterFiltered
    } catch (err) {
      console.error('Error fetching plan UCs:', err)
      if (!semester) {
        prevUcs.value = []
      }
      return []
    }
  }

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
    prevPlanData.value = null
    prevPlans.value = []
    prevUcs.value = []
    prevUcsBySemester.value = {}
  }

  return {
    wizardData,
    prevPlanData,
    prevPlans,
    prevUcs,
    prevUcsBySemester,
    planPreview,
    loadingPlan,
    fetchLatestPlan,
    fetchPrevPlans,
    fetchPrevPlanUcs,
    resetWizard
  }
})
