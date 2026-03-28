<template>
  <div class="max-w-screen-2xl mx-auto px-6 py-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white mb-2 tracking-tight">
          Course Inventory
        </h1>
        <p class="text-neutral-500 dark:text-neutral-400 text-sm">
          Manage and review academic programs across faculties.
        </p>
      </div>
      <button class="simple-button flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
        New Course
      </button>
    </div>

    <!-- Filters -->
    <div class="simple-card p-4 mb-8 flex flex-col sm:flex-row gap-4">
      <div class="flex-grow relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
        </div>
        <input
          type="text"
          placeholder="Search courses or codes..."
          class="simple-input pl-9"
        />
      </div>
      <div class="flex gap-4">
        <select class="simple-input sm:w-48 text-neutral-700 dark:text-neutral-300 appearance-none bg-white dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700">
          <option value="">All Departments</option>
          <option value="CS">Computer Science</option>
          <option value="ENG">Engineering</option>
        </select>
        <button class="simple-button-outline flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" /></svg>
          Filters
        </button>
      </div>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="course in courses"
        :key="course.id"
        class="simple-card p-6 flex flex-col hover:border-neutral-300 dark:hover:border-neutral-700 cursor-pointer"
        style="animation: fade-in-up 0.3s ease-out backwards;"
        :style="{ animationDelay: `${(course.id % 4) * 50}ms` }"
      >
        <div class="flex justify-between items-start mb-4">
          <span class="px-2 py-0.5 text-xs font-medium rounded bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400">
            {{ course.code }}
          </span>
          <span
            :class="{
              'text-emerald-600 dark:text-emerald-400': course.status === 'Active',
              'text-amber-600 dark:text-amber-500': course.status === 'Draft'
            }"
            class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-1.5"
          >
            <span class="w-1.5 h-1.5 rounded-full" :class="course.status === 'Active' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
            {{ course.status }}
          </span>
        </div>
        
        <h3 class="font-bold text-neutral-900 dark:text-white text-lg leading-tight mb-2">
          {{ course.name }}
        </h3>
        
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6 flex-grow line-clamp-2">
          {{ course.department }} Curriculum Pathway for standard modules.
        </p>

        <div class="flex items-center justify-between text-xs text-neutral-500 dark:text-neutral-400 border-t border-neutral-100 dark:border-neutral-800 pt-4 mt-auto">
          <span class="flex items-center gap-1.5 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" /></svg>
            {{ course.credits }} Credits
          </span>
          <span>
            Updated {{ course.updated }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const courses = ref([
  { id: 1, name: 'Advanced Mathematics', code: 'MATH401', department: 'Science', credits: 6, status: 'Active', updated: '2d ago' },
  { id: 2, name: 'Data Structures', code: 'CS202', department: 'Computer Science', credits: 6, status: 'Active', updated: '5d ago' },
  { id: 3, name: 'Quantum Physics', code: 'PHYS501', department: 'Science', credits: 8, status: 'Draft', updated: '1w ago' },
  { id: 4, name: 'Machine Learning', code: 'AI400', department: 'Computer Science', credits: 6, status: 'Active', updated: '2w ago' },
  { id: 5, name: 'Digital Logic', code: 'ENG105', department: 'Engineering', credits: 4, status: 'Active', updated: '1M ago' },
  { id: 6, name: 'Operating Systems', code: 'CS301', department: 'Computer Science', credits: 6, status: 'Active', updated: '2M ago' },
  { id: 7, name: 'Ethics in Tech', code: 'HUM201', department: 'Humanities', credits: 2, status: 'Draft', updated: '2M ago' },
  { id: 8, name: 'Database Systems', code: 'CS305', department: 'Computer Science', credits: 6, status: 'Active', updated: '3M ago' },
])
</script>
