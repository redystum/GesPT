<template>
  <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 sm:py-10 animate-fade-in-up">
    <!-- Header -->
    <header class="mb-8 pb-6 border-b border-neutral-200 dark:border-neutral-800">
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">
          {{ $t('brand.name') }}
        </p>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
          {{ $t('manual.title') }}
        </h1>
        <p class="mt-2 text-sm sm:text-base text-neutral-500 dark:text-neutral-400">
          {{ $t('manual.subtitle') }}
        </p>
      </div>
    </header>

    <div class="flex flex-row gap-4 sm:gap-8 lg:gap-10 items-start">
      <!-- Sidebar Navigation -->
      <aside class="w-40 sm:w-64 lg:w-80 shrink-0 sticky top-24 pr-2 sm:pr-4 scrollbar-thin">
        <nav class="space-y-4 sm:space-y-6">
          <div v-for="category in categories" :key="category.id" class="space-y-1 sm:space-y-2">
            <h3 class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500 flex items-center gap-1.5 sm:gap-2">
              <font-awesome-icon :icon="['fas', category.icon]" class="w-3.5 sm:w-4 text-[10px] sm:text-xs" />
              <span class="truncate">{{ $t(category.translationKey) }}</span>
            </h3>
            <ul class="space-y-0.5 sm:space-y-1 pl-2 sm:pl-4 border-l border-neutral-200 dark:border-neutral-800">
              <li v-for="section in category.sections" :key="section.id">
                <a
                  href="#"
                  @click.prevent="scrollToSection(section.id)"
                  :class="[
                    'block py-1 sm:py-1.5 px-2 sm:px-3 text-[11px] sm:text-sm rounded-md transition-all duration-150 truncate',
                    activeSection === section.id
                      ? 'bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 font-semibold shadow-xs'
                      : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100 hover:bg-neutral-100 dark:hover:bg-neutral-800/50'
                  ]"
                  :title="$t(`${section.translationKey}.title`)"
                >
                  {{ $t(`${section.translationKey}.title`) }}
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </aside>

      <!-- Main Content Area -->
      <main class="flex-1 min-w-0 space-y-12">
        <article
          v-for="category in categories"
          :key="category.id"
          class="space-y-6"
        >
          <!-- Category Header -->
          <div class="flex items-center gap-3 pb-3 border-b border-neutral-200 dark:border-neutral-800">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 border border-neutral-200 dark:border-neutral-800">
              <font-awesome-icon :icon="['fas', category.icon]" class="text-base" />
            </div>
            <div>
              <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                {{ $t(category.translationKey) }}
              </h2>
            </div>
          </div>

          <!-- Category Sections -->
          <div class="space-y-8">
            <section
              v-for="section in category.sections"
              :key="section.id"
              :id="section.id"
              class="scroll-mt-24 p-6 sm:p-8 bg-white dark:bg-[#1a1a1a] rounded-xl border border-neutral-200 dark:border-neutral-800 shadow-xs hover:shadow-sm transition-shadow duration-200"
            >
              <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <h3 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                  {{ $t(`${section.translationKey}.title`) }}
                </h3>
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium bg-neutral-100 dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700">
                  <font-awesome-icon :icon="['fas', 'shield-halved']" class="text-[10px]" />
                  {{ $t('manual.fields.permissions') }}: {{ $t(`${section.translationKey}.permissions`) }}
                </span>
              </div>

              <!-- Description -->
              <div class="mt-4">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-1">
                  {{ $t('manual.fields.description') }}
                </h4>
                <p class="text-neutral-600 dark:text-neutral-300 text-sm leading-relaxed">
                  {{ $t(`${section.translationKey}.desc`) }}
                </p>
              </div>

              <!-- Steps (Rendered with whitespace-pre-line) -->
              <div class="mt-6 bg-neutral-50 dark:bg-neutral-900/40 rounded-lg border border-neutral-100 dark:border-neutral-800/80 p-5">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-3 flex items-center gap-2">
                  <font-awesome-icon :icon="['fas', 'list-check']" />
                  <span>{{ $t('manual.fields.steps') }}</span>
                </h4>
                <div class="text-neutral-700 dark:text-neutral-300 text-sm leading-relaxed whitespace-pre-line space-y-2">
                  {{ $t(`${section.translationKey}.steps`) }}
                </div>
              </div>
            </section>
          </div>
        </article>
      </main>
    </div>

    <!-- Floating Back to Top Button -->
    <Transition name="fade">
      <button
        v-show="showBackToTop"
        @click="scrollToTop"
        class="fixed bottom-6 right-6 z-40 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 shadow-lg hover:scale-105 transition-transform border border-neutral-700 dark:border-neutral-200"
        :title="$t('manual.back_to_top')"
      >
        <font-awesome-icon :icon="['fas', 'arrow-up']" />
      </button>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useI18n } from '@/i18n'

const { locale } = useI18n()

const activeSection = ref('course_view')
const showBackToTop = ref(false)

const categories = [
  {
    id: 'courses',
    translationKey: 'manual.categories.courses',
    icon: 'graduation-cap',
    sections: [
      { id: 'course_view', translationKey: 'manual.sections.course_view' },
      { id: 'course_create', translationKey: 'manual.sections.course_create' },
      { id: 'course_edit', translationKey: 'manual.sections.course_edit' },
      { id: 'course_delete', translationKey: 'manual.sections.course_delete' }
    ]
  },
  {
    id: 'ucs',
    translationKey: 'manual.categories.ucs',
    icon: 'book',
    sections: [
      { id: 'uc_view', translationKey: 'manual.sections.uc_view' },
      { id: 'uc_create', translationKey: 'manual.sections.uc_create' },
      { id: 'uc_edit', translationKey: 'manual.sections.uc_edit' },
      { id: 'uc_delete', translationKey: 'manual.sections.uc_delete' }
    ]
  },
  {
    id: 'plans',
    translationKey: 'manual.categories.plans',
    icon: 'scroll',
    sections: [
      { id: 'plan_view_compare', translationKey: 'manual.sections.plan_view_compare' },
      { id: 'plan_create', translationKey: 'manual.sections.plan_create' },
      { id: 'plan_edit', translationKey: 'manual.sections.plan_edit' },
      { id: 'plan_delete', translationKey: 'manual.sections.plan_delete' }
    ]
  },
  {
    id: 'users',
    translationKey: 'manual.categories.users',
    icon: 'users',
    sections: [
      { id: 'user_view', translationKey: 'manual.sections.user_view' },
      { id: 'user_create', translationKey: 'manual.sections.user_create' },
      { id: 'user_edit', translationKey: 'manual.sections.user_edit' },
      { id: 'user_block', translationKey: 'manual.sections.user_block' },
      { id: 'user_delete', translationKey: 'manual.sections.user_delete' }
    ]
  },
  {
    id: 'roles',
    translationKey: 'manual.categories.roles',
    icon: 'key',
    sections: [
      { id: 'role_view', translationKey: 'manual.sections.role_view' },
      { id: 'role_create', translationKey: 'manual.sections.role_create' },
      { id: 'role_edit', translationKey: 'manual.sections.role_edit' },
      { id: 'role_assign', translationKey: 'manual.sections.role_assign' }
    ]
  },
  {
    id: 'domains',
    translationKey: 'manual.categories.domains',
    icon: 'at',
    sections: [
      { id: 'domain_add', translationKey: 'manual.sections.domain_add' },
      { id: 'domain_edit', translationKey: 'manual.sections.domain_edit' },
      { id: 'domain_remove', translationKey: 'manual.sections.domain_remove' }
    ]
  },
  {
    id: 'audit',
    translationKey: 'manual.categories.audit',
    icon: 'clipboard-list',
    sections: [
      { id: 'audit_view', translationKey: 'manual.sections.audit_view' }
    ]
  }
]

const scrollToSection = (id) => {
  const el = document.getElementById(id)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
    activeSection.value = id
  }
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

let observer = null

const checkScrollPosition = () => {
  showBackToTop.value = window.scrollY > 300
}

onMounted(() => {
  window.addEventListener('scroll', checkScrollPosition)

  const observerOptions = {
    root: null,
    rootMargin: '-10% 0px -75% 0px', // Highlights as section hits top half of the screen
    threshold: 0
  }

  observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        activeSection.value = entry.target.id
      }
    })
  }, observerOptions)

  // Track all sections
  categories.forEach((cat) => {
    cat.sections.forEach((sec) => {
      const el = document.getElementById(sec.id)
      if (el) observer.observe(el)
    })
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', checkScrollPosition)
  if (observer) {
    observer.disconnect()
  }
})
</script>

<style scoped>
/* Custom scrollbar styling for desktop navigation */
.scrollbar-thin::-webkit-scrollbar {
  width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 2px;
}
.dark .scrollbar-thin::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
}
</style>
