<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Navigation Back Link -->
    <div class="mb-6">
      <router-link
        v-if="uc?.id_curso"
        :to="{ name: 'course', params: { id: uc.id_curso } }"
        class="inline-flex items-center gap-2 text-sm font-medium text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100 transition-colors"
      >
        <font-awesome-icon :icon="['fas', 'arrow-left']" class="text-xs" />
        {{ $t('common.back') }}
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading.uc" class="flex flex-col items-center justify-center py-20">
      <font-awesome-icon :icon="['fas', 'circle-notch']" spin class="text-4xl text-neutral-400 dark:text-neutral-600 mb-4" />
      <span class="text-neutral-500 dark:text-neutral-400">{{ $t('common.loading') }}</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center max-w-lg mx-auto">
      <font-awesome-icon :icon="['fas', 'circle-exclamation']" class="text-3xl text-red-500 mb-3" />
      <h3 class="text-lg font-bold text-red-800 dark:text-red-300 mb-1">
        {{ $t('error.unexpected') }}
      </h3>
      <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
    </div>

    <!-- Main Content -->
    <div v-else-if="uc" class="space-y-8">
      <!-- Title & Header Bar -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-neutral-200 dark:border-neutral-800 pb-6">
        <div>
          <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
            {{ uc.curso ? (locale === 'pt' ? uc.curso.nome : (uc.curso.name || uc.curso.nome)) : '' }}
          </span>
          <h1 class="text-2xl md:text-3xl font-extrabold text-neutral-900 dark:text-neutral-50 mt-2">
            {{ locale === 'pt' ? uc.nome : (uc.name || uc.nome) }}
          </h1>
        </div>

        <!-- Mode Toggle & Action Buttons -->
        <div class="flex items-center gap-3" v-if="authStore.hasPermission('ucs.update')">
          <button
            v-if="!editMode"
            @click="editMode = true; saveError = null"
            class="inline-flex items-center gap-2 rounded-lg bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 px-4 py-2 text-sm font-semibold hover:bg-neutral-800 dark:hover:bg-neutral-200 transition-colors cursor-pointer"
          >
            <font-awesome-icon :icon="['fas', 'pencil']" class="text-xs" />
            {{ $t('admin.edit') }}
          </button>
          
          <template v-else>
            <button
              @click="cancelEdit"
              :disabled="saving"
              class="inline-flex items-center gap-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-[#1a1a1a] px-4 py-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors cursor-pointer disabled:opacity-50"
            >
              {{ $t('common.cancel') }}
            </button>
            <button
              @click="saveUc"
              :disabled="saving"
              class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-semibold transition-colors cursor-pointer disabled:opacity-50"
            >
              <font-awesome-icon v-if="saving" :icon="['fas', 'circle-notch']" spin class="text-xs" />
              <font-awesome-icon v-else :icon="['fas', 'check']" class="text-xs" />
              {{ $t('common.save') }}
            </button>
          </template>
        </div>
      </div>

      <!-- Save Error Banner -->
      <div v-if="saveError" class="p-4 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl text-sm flex justify-between items-center gap-3">
        <div class="flex items-center gap-2.5">
          <font-awesome-icon :icon="['fas', 'circle-exclamation']" class="text-red-500 text-base shrink-0" />
          <p class="font-medium">{{ saveError }}</p>
        </div>
        <button @click="saveError = null" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 p-1 cursor-pointer">
          <span class="text-base font-bold">&times;</span>
        </button>
      </div>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Columns: UC Parameters -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Basic details card -->
          <article class="bg-white dark:bg-[#1a1a1a] border border-neutral-200 dark:border-neutral-800 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4">
              {{ $t('uc_detail.general_info') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name PT -->
              <div>
                <label class="block text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">
                  {{ $t('course_detail.pt_name') }}
                </label>
                <input
                  v-if="editMode"
                  type="text"
                  v-model="form.nome"
                  class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 p-2.5 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <p v-else class="text-neutral-900 dark:text-neutral-100 font-medium">
                  {{ uc.nome }}
                </p>
              </div>

              <!-- Name EN -->
              <div>
                <label class="block text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">
                  {{ $t('course_detail.en_name') }}
                </label>
                <input
                  v-if="editMode"
                  type="text"
                  v-model="form.name"
                  class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 p-2.5 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <p v-else class="text-neutral-900 dark:text-neutral-100 font-medium">
                  {{ uc.name || $t('uc_detail.not_configured') }}
                </p>
              </div>

              <!-- Acronym -->
              <div>
                <label class="block text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">
                  {{ $t('course_detail.acronym') }}
                </label>
                <input
                  v-if="editMode"
                  type="text"
                  v-model="form.sigla"
                  class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 p-2.5 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <p v-else class="text-neutral-900 dark:text-neutral-100 font-medium">
                  {{ uc.sigla }}
                </p>
              </div>

              <!-- ECTS -->
              <div>
                <label class="block text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">
                  {{ $t('course_detail.ects') }}
                </label>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.ects"
                  min="1"
                  class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 p-2.5 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <p v-else class="text-neutral-900 dark:text-neutral-100 font-medium">
                  {{ uc.ects }}
                </p>
              </div>

              <!-- Semester -->
              <div>
                <label class="block text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">
                  {{ $t('course_detail.semester') }}
                </label>
                <select
                  v-if="editMode"
                  v-model.number="form.semestre"
                  class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 p-2.5 bg-white dark:bg-[#1a1a1a] dark:text-white"
                >
                  <option :value="1">1º</option>
                  <option :value="2">2º</option>
                </select>
                <p v-else class="text-neutral-900 dark:text-neutral-100 font-medium">
                  {{ uc.semestre ? `${uc.semestre}º` : 'N/A' }}
                </p>
              </div>

              <!-- Year -->
              <div>
                <label class="block text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider mb-1">
                  {{ $t('course_detail.year') }}
                </label>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.ano"
                  min="1"
                  max="6"
                  class="w-full rounded-lg border border-neutral-300 dark:border-neutral-700 p-2.5 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <p v-else class="text-neutral-900 dark:text-neutral-100 font-medium">
                  {{ uc.ano ? `${uc.ano}º` : 'N/A' }}
                </p>
              </div>
            </div>
          </article>

          <!-- Contact Hours breakdown card -->
          <article class="bg-white dark:bg-[#1a1a1a] border border-neutral-200 dark:border-neutral-800 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-4">
              {{ $t('uc_detail.workload_breakdown') }}
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
              <!-- Total Hours -->
              <div class="p-4 rounded-xl bg-neutral-50 dark:bg-neutral-800/40 border border-neutral-100 dark:border-neutral-800 text-center col-span-2 sm:col-span-3 md:col-span-1 flex flex-col justify-center">
                <span class="text-xs text-neutral-400 font-semibold uppercase tracking-wider mb-1 block">{{ $t('uc_detail.total_hours') }}</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas"
                  class="w-full text-center font-bold text-lg rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white block"
                />
                <span v-else class="text-2xl font-black text-neutral-800 dark:text-neutral-100 block">{{ uc.horas ?? 0 }}h</span>
              </div>

              <!-- T -->
              <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50 text-center">
                <span class="text-[10px] text-neutral-400 font-semibold mb-1 block" :title="$t('uc_detail.hours_t')">T</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas_t"
                  class="w-full text-center font-medium rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <span v-else class="font-bold text-neutral-700 dark:text-neutral-300">{{ uc.horas_t ?? 0 }}h</span>
              </div>

              <!-- TP -->
              <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50 text-center">
                <span class="text-[10px] text-neutral-400 font-semibold mb-1 block" :title="$t('uc_detail.hours_tp')">TP</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas_tp"
                  class="w-full text-center font-medium rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <span v-else class="font-bold text-neutral-700 dark:text-neutral-300">{{ uc.horas_tp ?? 0 }}h</span>
              </div>

              <!-- PL -->
              <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50 text-center">
                <span class="text-[10px] text-neutral-400 font-semibold mb-1 block" :title="$t('uc_detail.hours_pl')">PL</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas_pl"
                  class="w-full text-center font-medium rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <span v-else class="font-bold text-neutral-700 dark:text-neutral-300">{{ uc.horas_pl ?? 0 }}h</span>
              </div>

              <!-- TC -->
              <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50 text-center">
                <span class="text-[10px] text-neutral-400 font-semibold mb-1 block" :title="$t('uc_detail.hours_tc')">TC</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas_tc"
                  class="w-full text-center font-medium rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <span v-else class="font-bold text-neutral-700 dark:text-neutral-300">{{ uc.horas_tc ?? 0 }}h</span>
              </div>

              <!-- S -->
              <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50 text-center">
                <span class="text-[10px] text-neutral-400 font-semibold mb-1 block" :title="$t('uc_detail.hours_s')">S</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas_s"
                  class="w-full text-center font-medium rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <span v-else class="font-bold text-neutral-700 dark:text-neutral-300">{{ uc.horas_s ?? 0 }}h</span>
              </div>

              <!-- E -->
              <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50 text-center">
                <span class="text-[10px] text-neutral-400 font-semibold mb-1 block" :title="$t('uc_detail.hours_e')">E</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas_e"
                  class="w-full text-center font-medium rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <span v-else class="font-bold text-neutral-700 dark:text-neutral-300">{{ uc.horas_e ?? 0 }}h</span>
              </div>

              <!-- OT -->
              <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50 text-center">
                <span class="text-[10px] text-neutral-400 font-semibold mb-1 block" :title="$t('uc_detail.hours_ot')">OT</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas_ot"
                  class="w-full text-center font-medium rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <span v-else class="font-bold text-neutral-700 dark:text-neutral-300">{{ uc.horas_ot ?? 0 }}h</span>
              </div>

              <!-- O -->
              <div class="p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50 text-center">
                <span class="text-[10px] text-neutral-400 font-semibold mb-1 block" :title="$t('uc_detail.hours_o')">O</span>
                <input
                  v-if="editMode"
                  type="number"
                  v-model.number="form.horas_o"
                  class="w-full text-center font-medium rounded-lg border border-neutral-300 dark:border-neutral-700 p-1 bg-white dark:bg-[#1a1a1a] dark:text-white"
                />
                <span v-else class="font-bold text-neutral-700 dark:text-neutral-300">{{ uc.horas_o ?? 0 }}h</span>
              </div>
            </div>
          </article>
        </div>

        <!-- Right 1 Column: Options Management -->
        <div class="space-y-6">
          <article class="bg-white dark:bg-[#1a1a1a] border border-neutral-200 dark:border-neutral-800 rounded-xl p-6 shadow-sm h-full">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100 mb-2">
              {{ $t('uc_detail.options_section_title') }}
            </h2>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mb-6">
              {{ $t('uc_detail.options_section_desc') }}
            </p>

            <!-- Checkbox: Has Options / Optional slot (ONLY in edit mode) -->
            <div v-if="editMode" class="mb-6 flex items-start gap-3 p-3.5 rounded-lg bg-neutral-50 dark:bg-neutral-800/30 border border-neutral-100 dark:border-neutral-800/50">
              <input
                id="has-options-checkbox"
                type="checkbox"
                v-model="hasOptions"
                class="mt-1 h-4.5 w-4.5 text-blue-600 border-neutral-300 rounded focus:ring-blue-500 cursor-pointer"
              />
              <div>
                <label for="has-options-checkbox" class="font-bold text-sm text-neutral-800 dark:text-neutral-200 cursor-pointer">
                  {{ $t('uc_detail.is_optional_slot') }}
                </label>
                <span class="text-[11px] text-neutral-400 block mt-0.5">
                  {{ $t('uc_detail.is_optional_slot_hint') }}
                </span>
              </div>
            </div>

            <!-- In View Mode: if it has options, show options; otherwise show a simple message saying this UC doesn't have options -->
            <template v-if="!editMode">
              <div v-if="options.length === 0" class="text-sm text-neutral-500 dark:text-neutral-400 py-4 text-center border border-dashed border-neutral-200 dark:border-neutral-800 rounded-xl p-4 bg-neutral-50/50 dark:bg-neutral-800/10">
                {{ $t('uc_detail.no_options_available') }}
              </div>
              <div v-else class="space-y-3">
                <h3 class="text-xs font-bold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">
                  {{ $t('compare.equivalences') }}
                </h3>
                <ul class="space-y-2 max-h-[350px] overflow-y-auto pr-1">
                  <li
                    v-for="op in options"
                    :key="op.id"
                    class="p-3 border rounded-xl flex justify-between items-center bg-neutral-50 dark:bg-neutral-800/30 border-neutral-200/60 dark:border-neutral-800/60 transition-all"
                  >
                    <div class="min-w-0 flex-1 pr-2">
                      <div class="font-bold text-sm text-neutral-900 dark:text-neutral-200 truncate">
                        {{ locale === 'pt' ? op.nome : (op.name || op.nome) }}
                      </div>
                      <div class="text-[11px] text-neutral-400 mt-0.5 flex items-center gap-1.5 truncate">
                        <span class="font-semibold px-1 py-0.2 rounded bg-neutral-200/60 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 text-[9px]">{{ op.sigla }}</span>
                        <span>&middot;</span>
                        <span>{{ op.ects }} ECTS</span>
                        <span v-if="op.curso">&middot; {{ op.curso.sigla }}</span>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </template>

            <!-- In Edit Mode: if hasOptions is true, show form inputs to add/remove options -->
            <template v-else-if="hasOptions">
              <!-- Warning if not in any study plan -->
              <div
                v-if="uc.plano_ucs_count === 0"
                class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 text-amber-800 dark:text-amber-300 rounded-lg text-sm flex gap-3"
              >
                <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="text-lg mt-0.5 shrink-0" />
                <p>{{ $t('uc_detail.not_in_any_plan_warning') }}</p>
              </div>

              <div class="space-y-6">
                <!-- Add Option selection -->
                <div class="space-y-2">
                  <label class="block text-xs font-semibold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">
                    {{ $t('uc_detail.select_options_placeholder') }}
                  </label>
                  
                  <div class="flex gap-2">
                    <div class="relative flex-1">
                      <SearchableDropdown
                        v-model="selectedOptionUc"
                        :options="availableUcs"
                        :return-object="true"
                        :display-fn="(u) => `${u.nome} (${u.sigla}) - ${u.curso?.sigla || ''}`"
                        :search-fn="
                          (u, q) =>
                            normalizeString(u.nome?.toLowerCase() || '').includes(q) ||
                            normalizeString(u.sigla?.toLowerCase() || '').includes(q) ||
                            normalizeString(u.curso?.nome?.toLowerCase() || '').includes(q)
                        "
                        :placeholder="$t('wizard.uc_step.search_placeholder')"
                      />
                    </div>
                    <button
                      @click="addOption"
                      :disabled="!selectedOptionUc"
                      class="bg-blue-600 hover:bg-blue-700 text-white px-4.5 py-2.5 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed enabled:cursor-pointer shrink-0"
                    >
                      {{ $t('common.add_entry') }}
                    </button>
                  </div>
                </div>

                <!-- Options List -->
                <div class="space-y-3">
                  <h3 class="text-xs font-bold text-neutral-400 dark:text-neutral-500 uppercase tracking-wider">
                    {{ $t('compare.equivalences') }}
                  </h3>

                  <div v-if="options.length === 0" class="text-sm italic text-neutral-500 dark:text-neutral-500 py-4 text-center">
                    {{ $t('uc_detail.no_options_defined') }}
                  </div>

                  <ul v-else class="space-y-2 max-h-[350px] overflow-y-auto pr-1">
                    <li
                      v-for="op in options"
                      :key="op.id"
                      class="p-3 border rounded-xl flex justify-between items-center bg-neutral-50 dark:bg-neutral-800/30 border-neutral-200/60 dark:border-neutral-800/60 transition-all"
                    >
                      <div class="min-w-0 flex-1 pr-2">
                        <div class="font-bold text-sm text-neutral-900 dark:text-neutral-200 truncate">
                          {{ locale === 'pt' ? op.nome : (op.name || op.nome) }}
                        </div>
                        <div class="text-[11px] text-neutral-400 mt-0.5 flex items-center gap-1.5 truncate">
                          <span class="font-semibold px-1 py-0.2 rounded bg-neutral-200/60 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 text-[9px]">{{ op.sigla }}</span>
                          <span>&middot;</span>
                          <span>{{ op.ects }} ECTS</span>
                          <span v-if="op.curso">&middot; {{ op.curso.sigla }}</span>
                        </div>
                      </div>
                      
                      <button
                        @click="removeOption(op.id)"
                        class="text-red-500 hover:text-red-700 dark:hover:text-red-400 p-1.5 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-md transition-colors cursor-pointer shrink-0"
                        :title="$t('common.remove')"
                      >
                        <font-awesome-icon :icon="['fas', 'trash']" class="w-3.5 h-3.5" />
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </template>
          </article>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from '@/i18n'
import SearchableDropdown from '@/components/SearchableDropdown.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { locale, t } = useI18n()
const api = inject('api')

const ucId = computed(() => route.params.id)

const uc = ref(null)
const editMode = ref(false)
const saving = ref(false)
const error = ref(null)
const saveError = ref(null)

const form = ref({
  nome: '',
  name: '',
  sigla: '',
  ects: null,
  semestre: null,
  ano: null,
  horas: null,
  horas_t: null,
  horas_tp: null,
  horas_pl: null,
  horas_tc: null,
  horas_s: null,
  horas_e: null,
  horas_ot: null,
  horas_o: null
})

// Options list variables
const hasOptions = ref(false)
const options = ref([]) // array of UC objects
const dbAllUcs = ref([]) // loaded from database
const selectedOptionUc = ref(null)

const loading = ref({
  uc: false,
  allUcs: false
})

// Normalization function for searchable dropdown filtering
const normalizeString = (str) => {
  return str ? str.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : ''
}

// Compute which UCs can be selected as options (exclude self and already selected ones)
const availableUcs = computed(() => {
  if (!uc.value) return []
  return dbAllUcs.value.filter(u => {
    // Cannot select itself
    if (u.id === uc.value.id) return false
    // Cannot select already selected options
    return !options.value.some(op => op.id === u.id)
  })
})

const fetchUcDetails = async () => {
  loading.value.uc = true
  error.value = null
  try {
    const response = await api.get(`/ucs/${ucId.value}`)
    uc.value = response.data.data
    
    // Set up form values
    resetFormValues()
    
    // Set options list
    options.value = uc.value.opcoes || []
    hasOptions.value = options.value.length > 0 || (uc.value.plano_ucs_count > 0 && options.value.length > 0)
  } catch (err) {
    console.error('Error fetching UC:', err)
    error.value = err.response?.data?.message || err.message
  } finally {
    loading.value.uc = false
  }
}

const fetchAllUcs = async () => {
  loading.value.allUcs = true
  try {
    const response = await api.get('/ucs')
    dbAllUcs.value = response.data.data
  } catch (err) {
    console.error('Error fetching all UCs:', err)
  } finally {
    loading.value.allUcs = false
  }
}

const resetFormValues = () => {
  if (!uc.value) return
  form.value = {
    nome: uc.value.nome || '',
    name: uc.value.name || '',
    sigla: uc.value.sigla || '',
    ects: uc.value.ects,
    semestre: uc.value.semestre,
    ano: uc.value.ano,
    horas: uc.value.horas,
    horas_t: uc.value.horas_t,
    horas_tp: uc.value.horas_tp,
    horas_pl: uc.value.horas_pl,
    horas_tc: uc.value.horas_tc,
    horas_s: uc.value.horas_s,
    horas_e: uc.value.horas_e,
    horas_ot: uc.value.horas_ot,
    horas_o: uc.value.horas_o
  }
}

const cancelEdit = () => {
  editMode.value = false
  saveError.value = null
  resetFormValues()
  options.value = uc.value.opcoes || []
  hasOptions.value = options.value.length > 0
}

const addOption = () => {
  if (selectedOptionUc.value) {
    options.value.push(selectedOptionUc.value)
    selectedOptionUc.value = null
  }
}

const removeOption = (id) => {
  options.value = options.value.filter(op => op.id !== id)
}

const saveUc = async () => {
  saving.value = true
  saveError.value = null
  try {
    // If hasOptions is false, options array should be empty
    const optionsPayload = hasOptions.value ? options.value.map(op => op.id) : []
    
    const payload = {
      ...form.value,
      opcoes: optionsPayload
    }

    const response = await api.put(`/ucs/${ucId.value}`, payload)
    
    uc.value = response.data.data
    options.value = uc.value.opcoes || []
    hasOptions.value = options.value.length > 0
    editMode.value = false
  } catch (err) {
    console.error('Error saving UC details:', err)
    saveError.value = err.response?.data?.message || err.message
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchUcDetails()
  fetchAllUcs()
})

watch(ucId, () => {
  fetchUcDetails()
})
</script>

<style scoped>
/* Remove spin buttons from number inputs */
input[type='number']::-webkit-outer-spin-button,
input[type='number']::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type='number'] {
  -moz-appearance: textfield;
}
</style>
