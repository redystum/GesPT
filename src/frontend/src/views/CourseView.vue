<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up">
    <!-- Breadcrumbs / Back button -->
    <div class="mb-6">
      <router-link
        to="/"
        class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors"
      >
        <font-awesome-icon :icon="['fas', 'arrow-left']" />
        {{ $t('common.back') }}
      </router-link>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading.course"
      class="flex flex-col items-center justify-center h-[400px] rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/30"
    >
      <font-awesome-icon :icon="['fas', 'circle-notch']" spin class="text-4xl text-neutral-400 dark:text-neutral-600 mb-4" />
      <p class="text-neutral-500 dark:text-neutral-400 text-sm font-medium">{{ $t('common.loading') }}</p>
    </div>

    <!-- Error State -->
    <div
      v-else-if="error.course"
      class="flex flex-col items-center justify-center h-[400px] rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-6 text-center"
    >
      <font-awesome-icon :icon="['fas', 'exclamation-circle']" class="text-4xl text-red-500 dark:text-red-400 mb-4" />
      <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ $t('error.unexpected') }}</h3>
      <p class="mt-2 text-sm text-red-600 dark:text-red-400 max-w-md">{{ error.course }}</p>
      <button
        @click="init"
        class="mt-4 px-4 py-2 bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 rounded-md font-semibold text-sm hover:bg-neutral-800 dark:hover:bg-neutral-200 transition-colors cursor-pointer"
      >
        Retry
      </button>
    </div>

    <div v-else>
      <!-- Course Title / Hero Section -->
      <section
        class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-100 dark:bg-[#1a1a1a] transition-all p-6 sm:p-8 mb-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6"
      >
        <div>
          <div class="flex flex-wrap items-center gap-3">
            <span
              class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.06em] bg-neutral-200 dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 border border-neutral-300 dark:border-neutral-700"
            >
              {{ course.sigla }} · {{ course.codigo }}
            </span>
            <span class="text-xs text-neutral-400 font-medium">ID: #{{ course.id }}</span>
          </div>

          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-100 mt-3">
            {{ locale === 'pt' ? course.nome : (course.name || course.nome) }}
          </h1>

          <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400 font-medium">
            {{ ucs.length }} {{ $t('course_detail.ucs_table_title') }} &middot; {{ planos.length }} {{ $t('course_detail.plans_table_title') }}
          </p>
        </div>

        <div class="flex flex-wrap gap-3 shrink-0" v-if="authStore.hasPermission('cursos.update') || authStore.hasPermission('cursos.delete')">
          <button
            v-if="authStore.hasPermission('cursos.update')"
            @click="openEditCourseModal"
            class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-semibold transition-all bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700/80 border border-neutral-300 dark:border-neutral-700 cursor-pointer shadow-xs"
          >
            <font-awesome-icon :icon="['fas', 'pencil']" class="text-xs" />
            {{ $t('course_detail.edit_course') }}
          </button>
          <button
            v-if="authStore.hasPermission('cursos.delete')"
            @click="openDeleteCourseModal"
            class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-semibold transition-all bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 border border-red-200 dark:border-red-900/50 cursor-pointer"
          >
            <font-awesome-icon :icon="['fas', 'trash']" class="text-xs" />
            {{ $t('course_detail.delete_course') }}
          </button>
        </div>
      </section>

      <!-- Grid for Side-by-Side Tables -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        <!-- Left Table: Curricular Units (UCs) -->
        <article
          class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-xs flex flex-col overflow-hidden"
        >
          <div
            class="px-6 py-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between gap-4"
          >
            <div>
              <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                {{ $t('course_detail.ucs_table_title') }}
              </h2>
              <p class="text-xs text-neutral-400 mt-0.5">
                {{ ucs.length }} {{ $t('course_detail.ucs_table_title').toLowerCase() }}
              </p>
            </div>
            <button
              v-if="authStore.hasPermission('ucs.create')"
              @click="openAddUcModal"
              class="inline-flex items-center justify-center gap-1.5 rounded-md px-3.5 py-1.5 text-xs font-semibold transition-all bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 hover:bg-neutral-800 dark:hover:bg-neutral-200 cursor-pointer"
            >
              <font-awesome-icon :icon="['fas', 'plus']" class="text-[10px]" />
              {{ $t('course_detail.add_uc') }}
            </button>
          </div>

          <!-- Loader for UCs -->
          <div v-if="loading.ucs" class="p-8 flex justify-center items-center">
            <font-awesome-icon :icon="['fas', 'circle-notch']" spin class="text-2xl text-neutral-400" />
          </div>

          <div v-else-if="ucs.length === 0" class="p-8 text-center flex flex-col items-center">
            <div
              class="w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 mb-3"
            >
              <font-awesome-icon :icon="['fas', 'book-open']" class="text-lg" />
            </div>
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300">
              {{ $t('course_detail.no_ucs') }}
            </h3>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-sm border-collapse text-left">
              <thead>
                <tr class="bg-neutral-50 dark:bg-neutral-800/40 text-neutral-500 dark:text-neutral-400 text-xs font-semibold border-b border-neutral-100 dark:border-neutral-800">
                  <th class="px-6 py-4">{{ $t('wizard.uc_step.year') }}</th>
                  <th class="px-6 py-4">{{ $t('course_detail.pt_name') }}</th>
                  <th class="px-6 py-4">{{ $t('course_detail.acronym') }}</th>
                  <th class="px-6 py-4 text-center">{{ $t('course_detail.ects') }}</th>
                  <th class="px-6 py-4 text-center">{{ $t('course_detail.semester') }}</th>
                  <th class="px-6 py-4 text-center">{{ $t('course_detail.hours') }}</th>
                  <th v-if="authStore.hasPermission('ucs.update') || authStore.hasPermission('ucs.delete')" class="px-6 py-4 text-right">{{ $t('course_detail.actions') }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/60">
                <tr
                  v-for="uc in sortedUcs"
                  :key="uc.id"
                  class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/20 transition-colors"
                >
                  <td class="px-6 py-3.5 font-medium text-neutral-600 dark:text-neutral-400">
                    {{ uc.ano ? `${uc.ano}º` : 'N/A' }}
                  </td>
                  <td class="px-6 py-3.5 font-semibold text-neutral-900 dark:text-neutral-100">
                    <div>
                      <p>{{ locale === 'pt' ? uc.nome : (uc.name || uc.nome) }}</p>
                      <p v-if="locale === 'pt' && uc.name" class="text-[11px] font-normal text-neutral-400 dark:text-neutral-500">
                        {{ uc.name }}
                      </p>
                      <p v-else-if="locale === 'en' && uc.name && uc.nome !== uc.name" class="text-[11px] font-normal text-neutral-400 dark:text-neutral-500">
                        {{ uc.nome }}
                      </p>
                    </div>
                  </td>
                  <td class="px-6 py-3.5">
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400">
                      {{ uc.sigla }}
                    </span>
                  </td>
                  <td class="px-6 py-3.5 text-center font-bold text-neutral-800 dark:text-neutral-200">
                    {{ uc.ects }}
                  </td>
                  <td class="px-6 py-3.5 text-center text-neutral-600 dark:text-neutral-300">
                    {{ uc.semestre ? `${uc.semestre}º` : 'N/A' }}
                  </td>
                  <td class="px-6 py-3.5 text-center text-neutral-600 dark:text-neutral-300">
                    {{ uc.horas }}h
                  </td>
                  <td v-if="authStore.hasPermission('ucs.update') || authStore.hasPermission('ucs.delete')" class="px-6 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex gap-2">
                      <button
                        v-if="authStore.hasPermission('ucs.update')"
                        @click="openEditUcModal(uc)"
                        class="p-1.5 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer"
                        :title="$t('course_detail.edit_uc')"
                      >
                        <font-awesome-icon :icon="['fas', 'pencil']" class="w-3.5 h-3.5" />
                      </button>
                      <button
                        v-if="authStore.hasPermission('ucs.delete')"
                        @click="openDeleteUcModal(uc)"
                        class="p-1.5 rounded-md text-neutral-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 cursor-pointer"
                        :title="$t('course_detail.delete_uc')"
                      >
                        <font-awesome-icon :icon="['fas', 'trash']" class="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </article>

        <!-- Right Table: Study Plans (Planos) -->
        <article
          class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#1a1a1a] shadow-xs flex flex-col overflow-hidden"
        >
          <div
            class="px-6 py-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between gap-4"
          >
            <div>
              <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                {{ $t('course_detail.plans_table_title') }}
              </h2>
              <p class="text-xs text-neutral-400 mt-0.5">
                {{ planos.length }} {{ $t('course_detail.plans_table_title').toLowerCase() }}
              </p>
            </div>
            <router-link v-if="authStore.hasPermission('planos_estudo.create')" :to="{ name: 'wizard'}"
              class="inline-flex items-center justify-center gap-1.5 rounded-md px-3.5 py-1.5 text-xs font-semibold transition-all bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 hover:bg-neutral-800 dark:hover:bg-neutral-200 cursor-pointer"
            >
              <font-awesome-icon :icon="['fas', 'plus']" class="text-[10px]" />
              {{ $t('course_detail.add_plan') }}
            </router-link>
          </div>

          <!-- Loader for Planos -->
          <div v-if="loading.planos" class="p-8 flex justify-center items-center">
            <font-awesome-icon :icon="['fas', 'circle-notch']" spin class="text-2xl text-neutral-400" />
          </div>

          <div v-else-if="planos.length === 0" class="p-8 text-center flex flex-col items-center">
            <div
              class="w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-400 mb-3"
            >
              <font-awesome-icon :icon="['fas', 'scroll']" class="text-lg" />
            </div>
            <h3 class="text-sm font-bold text-neutral-700 dark:text-neutral-300">
              {{ $t('course_detail.no_plans') }}
            </h3>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-sm border-collapse text-left">
              <thead>
                <tr class="bg-neutral-50 dark:bg-neutral-800/40 text-neutral-500 dark:text-neutral-400 text-xs font-semibold border-b border-neutral-100 dark:border-neutral-800">
                  <th class="px-6 py-4">ID</th>
                  <th class="px-6 py-4">{{ $t('wizard.plan_step.academic_year') }}</th>
                  <th class="px-6 py-4 text-center">{{ $t('course_detail.ucs_table_title') }}</th>
                  <th class="px-6 py-4 text-right">{{ $t('course_detail.actions') }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/60">
                <tr
                  v-for="plan in planos"
                  :key="plan.id"
                  class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/20 transition-colors"
                >
                  <td class="px-6 py-3.5 font-medium text-neutral-400">
                    #{{ plan.id }}
                  </td>
                  <td class="px-6 py-3.5 font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ plan.ano }}
                  </td>
                  <td class="px-6 py-3.5 text-center">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-900/10 text-blue-600 dark:text-blue-400">
                      {{ plan.ucs }} UCs
                    </span>
                  </td>
                  <td class="px-6 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex gap-2">
                      <router-link
                        :to="{ path: '/compare', query: { course: course.id, plan: plan.id } }"
                        class="p-1.5 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800"
                        title="Compare/View Equivalences"
                      >
                        <font-awesome-icon :icon="['fas', 'eye']" class="w-3.5 h-3.5" />
                      </router-link>
                      <button
                        v-if="authStore.hasPermission('planos_estudo.update')"
                        @click="openEditPlanModal(plan)"
                        class="p-1.5 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer"
                        :title="$t('course_detail.edit_plan')"
                      >
                        <font-awesome-icon :icon="['fas', 'pencil']" class="w-3.5 h-3.5" />
                      </button>
                      <button
                        v-if="authStore.hasPermission('planos_estudo.delete')"
                        @click="openDeletePlanModal(plan)"
                        class="p-1.5 rounded-md text-neutral-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 cursor-pointer"
                        :title="$t('course_detail.delete_plan')"
                      >
                        <font-awesome-icon :icon="['fas', 'trash']" class="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </article>
      </div>
    </div>

    <!-- Modals System -->
    
    <!-- 1. COURSE EDIT MODAL -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="modals.course" @click.self="modals.course = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
          <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-2xl max-w-lg w-full overflow-hidden animate-zoom-in">
            <div class="px-6 py-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
              <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                {{ $t('course_detail.edit_course') }}
              </h3>
              <button @click="modals.course = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 cursor-pointer">
                <font-awesome-icon :icon="['fas', 'times']" />
              </button>
            </div>

            <form @submit.prevent="saveCourse" class="p-6 space-y-4">
              <div v-if="modalError" class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 rounded-lg text-xs text-red-600 dark:text-red-400">
                {{ modalError }}
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                  {{ $t('course_detail.pt_name') }} *
                </label>
                <input
                  v-model="modalData.course.nome"
                  type="text"
                  required
                  class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                  {{ $t('course_detail.en_name') }}
                </label>
                <input
                  v-model="modalData.course.name"
                  type="text"
                  class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.acronym') }} *
                  </label>
                  <input
                    v-model="modalData.course.sigla"
                    type="text"
                    required
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.code') }} *
                  </label>
                  <input
                    v-model="modalData.course.codigo"
                    type="text"
                    required
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                  />
                </div>
              </div>

              <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-end gap-3">
                <button
                  type="button"
                  @click="modals.course = false"
                  class="px-4 py-2 border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-md text-sm font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
                >
                  {{ $t('common.cancel') }}
                </button>
                <button
                  type="submit"
                  :disabled="saving"
                  class="px-4 py-2 bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 rounded-md text-sm font-semibold hover:bg-neutral-800 dark:hover:bg-neutral-200 disabled:opacity-50 transition-colors cursor-pointer"
                >
                  <font-awesome-icon v-if="saving" :icon="['fas', 'circle-notch']" spin class="mr-1.5 text-xs" />
                  {{ $t('common.save') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- 2. UC ADD/EDIT MODAL -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="modals.uc" @click.self="modals.uc = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs overflow-y-auto">
          <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-2xl max-w-xl w-full my-8 max-h-[90vh] flex flex-col animate-zoom-in">
            <div class="px-6 py-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between shrink-0">
              <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                {{ isEditing ? $t('course_detail.edit_uc') : $t('course_detail.add_uc') }}
              </h3>
              <button @click="modals.uc = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 cursor-pointer">
                <font-awesome-icon :icon="['fas', 'times']" />
              </button>
            </div>

            <form @submit.prevent="saveUc" class="p-6 space-y-4 overflow-y-auto flex-1">
              <div v-if="modalError" class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 rounded-lg text-xs text-red-600 dark:text-red-400">
                {{ modalError }}
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                  {{ $t('course_detail.pt_name') }} *
                </label>
                <input
                  v-model="modalData.uc.nome"
                  type="text"
                  required
                  class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                  {{ $t('course_detail.en_name') }}
                </label>
                <input
                  v-model="modalData.uc.name"
                  type="text"
                  class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.acronym') }} *
                  </label>
                  <input
                    v-model="modalData.uc.sigla"
                    type="text"
                    required
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.ects') }} *
                  </label>
                  <input
                    v-model.number="modalData.uc.ects"
                    type="number"
                    required
                    min="1"
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                  />
                </div>
              </div>

              <div class="grid grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.semester') }}
                  </label>
                  <select
                    v-model.number="modalData.uc.semestre"
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                  >
                    <option :value="null">N/A</option>
                    <option :value="1">1º Semestre</option>
                    <option :value="2">2º Semestre</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.year') }}
                  </label>
                  <input
                    v-model.number="modalData.uc.ano"
                    type="number"
                    min="1"
                    max="6"
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                    placeholder="e.g. 1"
                  />
                </div>

                <div>
                  <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                    {{ $t('course_detail.hours') }} *
                  </label>
                  <input
                    v-model.number="modalData.uc.horas"
                    type="number"
                    required
                    min="1"
                    class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                  />
                </div>
              </div>

              <!-- Accordion for Detailed Workload Hours -->
              <div class="border border-neutral-200 dark:border-neutral-800 rounded-lg overflow-hidden mt-4">
                <button
                  type="button"
                  @click="showWorkloadDetails = !showWorkloadDetails"
                  class="w-full bg-neutral-50 dark:bg-neutral-800/20 px-4 py-3 flex items-center justify-between text-sm font-bold text-neutral-700 dark:text-neutral-300 cursor-pointer"
                >
                  <span>{{ $t('wizard.uc_step.workload_title') }} (Optional)</span>
                  <font-awesome-icon :icon="showWorkloadDetails ? ['fas', 'chevron-up'] : ['fas', 'chevron-down']" class="text-xs" />
                </button>

                <div v-show="showWorkloadDetails" class="p-4 bg-white dark:bg-neutral-900 border-t border-neutral-100 dark:border-neutral-800 grid grid-cols-4 gap-3">
                  <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-0.5">T</label>
                    <input v-model.number="modalData.uc.horas_t" type="number" min="0" class="w-full rounded border border-neutral-200 dark:border-neutral-700 p-1.5 text-xs bg-white dark:bg-neutral-900 dark:text-white" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-0.5">TP</label>
                    <input v-model.number="modalData.uc.horas_tp" type="number" min="0" class="w-full rounded border border-neutral-200 dark:border-neutral-700 p-1.5 text-xs bg-white dark:bg-neutral-900 dark:text-white" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-0.5">PL</label>
                    <input v-model.number="modalData.uc.horas_pl" type="number" min="0" class="w-full rounded border border-neutral-200 dark:border-neutral-700 p-1.5 text-xs bg-white dark:bg-neutral-900 dark:text-white" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-0.5">TC</label>
                    <input v-model.number="modalData.uc.horas_tc" type="number" min="0" class="w-full rounded border border-neutral-200 dark:border-neutral-700 p-1.5 text-xs bg-white dark:bg-neutral-900 dark:text-white" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-0.5">S</label>
                    <input v-model.number="modalData.uc.horas_s" type="number" min="0" class="w-full rounded border border-neutral-200 dark:border-neutral-700 p-1.5 text-xs bg-white dark:bg-neutral-900 dark:text-white" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-0.5">E</label>
                    <input v-model.number="modalData.uc.horas_e" type="number" min="0" class="w-full rounded border border-neutral-200 dark:border-neutral-700 p-1.5 text-xs bg-white dark:bg-neutral-900 dark:text-white" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-0.5">OT</label>
                    <input v-model.number="modalData.uc.horas_ot" type="number" min="0" class="w-full rounded border border-neutral-200 dark:border-neutral-700 p-1.5 text-xs bg-white dark:bg-neutral-900 dark:text-white" />
                  </div>
                  <div>
                    <label class="block text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 mb-0.5">O</label>
                    <input v-model.number="modalData.uc.horas_o" type="number" min="0" class="w-full rounded border border-neutral-200 dark:border-neutral-700 p-1.5 text-xs bg-white dark:bg-neutral-900 dark:text-white" />
                  </div>
                </div>
              </div>

              <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-end gap-3 shrink-0">
                <button
                  type="button"
                  @click="modals.uc = false"
                  class="px-4 py-2 border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-md text-sm font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
                >
                  {{ $t('common.cancel') }}
                </button>
                <button
                  type="submit"
                  :disabled="saving"
                  class="px-4 py-2 bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 rounded-md text-sm font-semibold hover:bg-neutral-800 dark:hover:bg-neutral-200 disabled:opacity-50 transition-colors cursor-pointer"
                >
                  <font-awesome-icon v-if="saving" :icon="['fas', 'circle-notch']" spin class="mr-1.5 text-xs" />
                  {{ $t('common.save') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- 3. PLAN ADD/EDIT MODAL -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="modals.plan" @click.self="modals.plan = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
          <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-2xl max-w-sm w-full overflow-hidden animate-zoom-in">
            <div class="px-6 py-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
              <h3 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                {{ isEditing ? $t('course_detail.edit_plan') : $t('course_detail.add_plan') }}
              </h3>
              <button @click="modals.plan = false" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 cursor-pointer">
                <font-awesome-icon :icon="['fas', 'times']" />
              </button>
            </div>

            <form @submit.prevent="savePlan" class="p-6 space-y-4">
              <div v-if="modalError" class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 rounded-lg text-xs text-red-600 dark:text-red-400">
                {{ modalError }}
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-1">
                  {{ $t('wizard.plan_step.academic_year') }} *
                </label>
                <input
                  v-model="modalData.plan.ano"
                  type="text"
                  required
                  :placeholder="$t('wizard.plan_step.academic_year_placeholder')"
                  class="w-full rounded-md border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 outline-hidden focus:border-neutral-500 focus:ring-2 focus:ring-neutral-500/20"
                />
                <p class="text-[11px] text-neutral-400 mt-1">
                  Format: 2026/27 or 2026/2027
                </p>
              </div>

              <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex justify-end gap-3">
                <button
                  type="button"
                  @click="modals.plan = false"
                  class="px-4 py-2 border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-md text-sm font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
                >
                  {{ $t('common.cancel') }}
                </button>
                <button
                  type="submit"
                  :disabled="saving"
                  class="px-4 py-2 bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-950 rounded-md text-sm font-semibold hover:bg-neutral-800 dark:hover:bg-neutral-200 disabled:opacity-50 transition-colors cursor-pointer"
                >
                  <font-awesome-icon v-if="saving" :icon="['fas', 'circle-notch']" spin class="mr-1.5 text-xs" />
                  {{ $t('common.save') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- 4. DELETE CONFIRMATION MODAL -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="modals.delete" @click.self="modals.delete = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
          <div class="bg-white dark:bg-[#1a1a1a] rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-2xl max-w-md w-full overflow-hidden animate-zoom-in">
            <div class="p-6 flex gap-4">
              <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                <font-awesome-icon :icon="['fas', 'exclamation-triangle']" />
              </div>
              <div>
                <h3 class="text-base font-bold text-neutral-900 dark:text-neutral-100">
                  Confirm Deletion
                </h3>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2 leading-relaxed">
                  {{ deleteConfig.message }}
                </p>
              </div>
            </div>

            <div class="bg-neutral-50 dark:bg-neutral-800/40 px-6 py-4 flex justify-end gap-3">
              <button
                type="button"
                @click="modals.delete = false"
                class="px-4 py-2 border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-md text-sm font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
              >
                {{ $t('common.cancel') }}
              </button>
              <button
                type="button"
                @click="confirmDeletion"
                :disabled="saving"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-semibold disabled:opacity-50 transition-colors cursor-pointer"
              >
                <font-awesome-icon v-if="saving" :icon="['fas', 'circle-notch']" spin class="mr-1.5 text-xs" />
                {{ $t('common.delete') }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { inject, ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from '@/i18n'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const { locale, t } = useI18n()
const api = inject('api')
const authStore = useAuthStore()

const courseId = computed(() => route.params.id)

// State
const course = ref({})
const ucs = ref([])
const planos = ref([])
const showWorkloadDetails = ref(false)

const loading = reactive({
  course: true,
  ucs: false,
  planos: false
})

const error = reactive({
  course: null
})

// Modals State
const modals = reactive({
  course: false,
  uc: false,
  plan: false,
  delete: false
})

const isEditing = ref(false)
const saving = ref(false)
const modalError = ref(null)

const modalData = reactive({
  course: {
    nome: '',
    name: '',
    sigla: '',
    codigo: ''
  },
  uc: {
    id: null,
    nome: '',
    name: '',
    sigla: '',
    ects: 1,
    horas: 1,
    semestre: null,
    ano: null,
    horas_t: null,
    horas_tp: null,
    horas_pl: null,
    horas_tc: null,
    horas_s: null,
    horas_e: null,
    horas_ot: null,
    horas_o: null
  },
  plan: {
    id: null,
    ano: ''
  }
})

// Delete configuration
const deleteConfig = reactive({
  type: '', // 'course', 'uc', 'plan'
  targetId: null,
  message: ''
})

// Sorted UCs (by year and then semester)
const sortedUcs = computed(() => {
  return [...ucs.value].sort((a, b) => {
    if (a.ano !== b.ano) {
      return (a.ano || 99) - (b.ano || 99)
    }
    return (a.semestre || 99) - (b.semestre || 99)
  })
})

// Initialization
const init = async () => {
  loading.course = true
  error.course = null
  try {
    await fetchCourse()
    await Promise.all([fetchUcs(), fetchPlanos()])
  } catch (err) {
    console.error('Failed to load course details:', err)
    error.course = err.response?.data?.message || err.message || 'Error fetching course data.'
  } finally {
    loading.course = false
  }
}

onMounted(() => {
  init()
})

// Fetch methods
const fetchCourse = async () => {
  const res = await api.get(`/cursos/${courseId.value}`)
  course.value = res.data.data
}

const fetchUcs = async () => {
  loading.ucs = true
  try {
    const res = await api.get(`/cursos/${courseId.value}/ucs`)
    ucs.value = res.data.data
  } finally {
    loading.ucs = false
  }
}

const fetchPlanos = async () => {
  loading.planos = true
  try {
    const res = await api.get(`/cursos/${courseId.value}/planos`)
    planos.value = res.data.data
  } finally {
    loading.planos = false
  }
}

// -------------------------------------------------------------
// Course Actions
// -------------------------------------------------------------
const openEditCourseModal = () => {
  modalError.value = null
  modalData.course = {
    nome: course.value.nome,
    name: course.value.name || '',
    sigla: course.value.sigla,
    codigo: course.value.codigo
  }
  modals.course = true
}

const saveCourse = async () => {
  saving.value = true
  modalError.value = null
  try {
    const res = await api.put(`/cursos/${courseId.value}`, modalData.course)
    course.value = res.data.data
    modals.course = false
  } catch (err) {
    console.error('Failed to save course:', err)
    modalError.value = err.response?.data?.message || 'Failed to save changes.'
  } finally {
    saving.value = false
  }
}

const openDeleteCourseModal = () => {
  deleteConfig.type = 'course'
  deleteConfig.targetId = courseId.value
  deleteConfig.message = t('course_detail.confirm_delete_course')
  modals.delete = true
}

// -------------------------------------------------------------
// UC Actions
// -------------------------------------------------------------
const openAddUcModal = () => {
  isEditing.value = false
  modalError.value = null
  showWorkloadDetails.value = false
  modalData.uc = {
    id: null,
    nome: '',
    name: '',
    sigla: '',
    ects: 6,
    horas: 60,
    semestre: 1,
    ano: 1,
    horas_t: null,
    horas_tp: null,
    horas_pl: null,
    horas_tc: null,
    horas_s: null,
    horas_e: null,
    horas_ot: null,
    horas_o: null
  }
  modals.uc = true
}

const openEditUcModal = (uc) => {
  isEditing.value = true
  modalError.value = null
  showWorkloadDetails.value = false
  modalData.uc = {
    id: uc.id,
    nome: uc.nome,
    name: uc.name || '',
    sigla: uc.sigla,
    ects: uc.ects,
    horas: uc.horas,
    semestre: uc.semestre,
    ano: uc.ano,
    horas_t: uc.horas_t,
    horas_tp: uc.horas_tp,
    horas_pl: uc.horas_pl,
    horas_tc: uc.horas_tc,
    horas_s: uc.horas_s,
    horas_e: uc.horas_e,
    horas_ot: uc.horas_ot,
    horas_o: uc.horas_o
  }
  // Check if there are workload hours defined to expand the workload section
  if (uc.horas_t != null || uc.horas_tp != null || uc.horas_pl != null || uc.horas_tc != null) {
    showWorkloadDetails.value = true
  }
  modals.uc = true
}

const saveUc = async () => {
  saving.value = true
  modalError.value = null
  
  // Build payload and clean empty strings or nulls to match rules
  const payload = {
    nome: modalData.uc.nome,
    name: modalData.uc.name || null,
    sigla: modalData.uc.sigla,
    ects: parseInt(modalData.uc.ects),
    horas: parseInt(modalData.uc.horas),
    id_curso: parseInt(courseId.value),
    semestre: modalData.uc.semestre != null ? parseInt(modalData.uc.semestre) : null,
    ano: modalData.uc.ano != null ? parseInt(modalData.uc.ano) : null,
    horas_t: modalData.uc.horas_t != null ? parseInt(modalData.uc.horas_t) : null,
    horas_tp: modalData.uc.horas_tp != null ? parseInt(modalData.uc.horas_tp) : null,
    horas_pl: modalData.uc.horas_pl != null ? parseInt(modalData.uc.horas_pl) : null,
    horas_tc: modalData.uc.horas_tc != null ? parseInt(modalData.uc.horas_tc) : null,
    horas_s: modalData.uc.horas_s != null ? parseInt(modalData.uc.horas_s) : null,
    horas_e: modalData.uc.horas_e != null ? parseInt(modalData.uc.horas_e) : null,
    horas_ot: modalData.uc.horas_ot != null ? parseInt(modalData.uc.horas_ot) : null,
    horas_o: modalData.uc.horas_o != null ? parseInt(modalData.uc.horas_o) : null
  }

  try {
    if (isEditing.value) {
      await api.put(`/ucs/${modalData.uc.id}`, payload)
    } else {
      await api.post(`/ucs`, payload)
    }
    await fetchUcs()
    modals.uc = false
  } catch (err) {
    console.error('Failed to save UC:', err)
    modalError.value = err.response?.data?.message || 'Failed to save Curricular Unit.'
  } finally {
    saving.value = false
  }
}

const openDeleteUcModal = (uc) => {
  deleteConfig.type = 'uc'
  deleteConfig.targetId = uc.id
  deleteConfig.message = t('course_detail.confirm_delete_uc') + ` (${uc.nome})`
  modals.delete = true
}

// -------------------------------------------------------------
// Study Plan Actions
// -------------------------------------------------------------

const openEditPlanModal = (plan) => {
  isEditing.value = true
  modalError.value = null
  modalData.plan = {
    id: plan.id,
    ano: plan.ano
  }
  modals.plan = true
}

const savePlan = async () => {
  // Validate plan academic year sequence
  const yearPattern = /^20\d{2}\/(20)?\d{2}$/
  if (!yearPattern.test(modalData.plan.ano)) {
    modalError.value = 'Year must be in format 20xx/xx or 20xx/20xx'
    return
  }

  const parts = modalData.plan.ano.split('/')
  const year1 = parseInt(parts[0])
  let year2 = parseInt(parts[1])
  if (parts[1].length === 2) {
    year2 = 2000 + year2
  }
  if (year2 !== year1 + 1) {
    modalError.value = 'The second year must be exactly one year after the first (e.g. 2026/27).'
    return
  }

  saving.value = true
  modalError.value = null
  const payload = {
    ano: modalData.plan.ano,
    id_curso: parseInt(courseId.value)
  }

  try {
    if (isEditing.value) {
      await api.put(`/planos-estudo/${modalData.plan.id}`, payload)
    } else {
      await api.post(`/planos-estudo`, payload)
    }
    await fetchPlanos()
    modals.plan = false
  } catch (err) {
    console.error('Failed to save Plan:', err)
    modalError.value = err.response?.data?.message || 'Failed to save Study Plan.'
  } finally {
    saving.value = false
  }
}

const openDeletePlanModal = (plan) => {
  deleteConfig.type = 'plan'
  deleteConfig.targetId = plan.id
  deleteConfig.message = t('course_detail.confirm_delete_plan') + ` (${plan.ano})`
  modals.delete = true
}

// -------------------------------------------------------------
// Combined Deletion Confirmation
// -------------------------------------------------------------
const confirmDeletion = async () => {
  saving.value = true
  try {
    if (deleteConfig.type === 'course') {
      await api.delete(`/cursos/${deleteConfig.targetId}`)
      modals.delete = false
      router.push('/')
    } else if (deleteConfig.type === 'uc') {
      await api.delete(`/ucs/${deleteConfig.targetId}`)
      await fetchUcs()
      modals.delete = false
    } else if (deleteConfig.type === 'plan') {
      await api.delete(`/planos-estudo/${deleteConfig.targetId}`)
      await fetchPlanos()
      modals.delete = false
    }
  } catch (err) {
    console.error('Failed to delete resource:', err)
    alert(err.response?.data?.message || 'Failed to delete resource.')
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
/* Animations */
.animate-fade-in-up {
  animation: fade-in-up 0.4s ease-out forwards;
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-zoom-in {
  animation: zoom-in 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes zoom-in {
  from {
    opacity: 0;
    transform: scale(0.96);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Vue Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>