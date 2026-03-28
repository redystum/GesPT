<template>
  <div class="max-w-screen-2xl mx-auto px-6 py-10 animate-fade-in-up">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-white mb-2 tracking-tight">
          Plan Comparison
        </h1>
        <p class="text-neutral-500 dark:text-neutral-400 text-sm">
          Technical cross-analysis: Legacy vs Proposed Structure
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button class="simple-button-outline">
          Export CSV
        </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1">
            Total Variance
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">0 ECTS</p>
        </div>
      </div>
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1">
            Equivalence Rate
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">84%</p>
        </div>
      </div>
      <div class="simple-card p-5 flex justify-between items-center">
        <div>
          <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-1">
            New Modules
          </p>
          <p class="text-2xl font-bold text-neutral-900 dark:text-white">12</p>
        </div>
      </div>
    </div>

    <!-- Comparison Table -->
    <div class="simple-card overflow-hidden">
      <div class="grid grid-cols-2 text-xs font-semibold text-neutral-500 dark:text-neutral-400 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50">
        <div class="p-4 border-r border-neutral-200 dark:border-neutral-800 flex justify-between items-center">
          <span>Plan 2015/16 Legacy</span>
        </div>
        <div class="p-4 flex justify-between items-center">
          <span class="text-neutral-900 dark:text-white">Plan 2025/26 Proposed</span>
        </div>
      </div>

      <!-- Item 1 -->
      <div class="grid grid-cols-2 border-b border-neutral-100 dark:border-neutral-800/50">
        <div class="p-5 border-r border-neutral-100 dark:border-neutral-800/50">
          <p class="font-medium text-neutral-900 dark:text-white mb-1">Mathematics I</p>
          <p class="text-xs text-neutral-500 flex items-center gap-2">
            UC_7721 • 6 ECTS
          </p>
        </div>
        <div class="p-5 flex justify-between items-start">
          <div>
            <p class="font-medium text-neutral-900 dark:text-white mb-1">Advanced Calculus I</p>
            <p class="text-xs text-neutral-500 flex items-center gap-2">
              UC_9901 • 6 ECTS
            </p>
          </div>
          <span class="text-[10px] font-bold text-neutral-500 bg-neutral-100 dark:bg-neutral-800 px-2 py-1 rounded">EQUIV</span>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="grid grid-cols-2 border-b border-neutral-100 dark:border-neutral-800/50">
        <div class="p-5 border-r border-neutral-100 dark:border-neutral-800/50 flex items-center text-neutral-500 line-through">
          <p>Fortran Legacy Systems</p>
        </div>
        <div class="p-5 flex items-center">
          <span class="text-[10px] font-bold text-neutral-500 bg-neutral-100 dark:bg-neutral-800 px-2 py-1 rounded">DROP</span>
        </div>
      </div>
      
      <!-- NEW -->
      <div class="grid grid-cols-2">
        <div class="p-5 border-r border-neutral-100 dark:border-neutral-800/50 flex items-center">
          <span class="text-xs italic text-neutral-400">No ancestor module</span>
        </div>
        <div class="p-5 flex justify-between items-center">
          <div>
            <p class="font-medium text-neutral-900 dark:text-white mb-1">AI Ethics & Governance</p>
            <p class="text-xs text-neutral-500 flex items-center gap-2">
              UC_2050 • 6 ECTS
            </p>
          </div>
          <span class="text-[10px] font-bold text-neutral-900 dark:text-white bg-neutral-200 dark:bg-neutral-700 px-2 py-1 rounded">NEW</span>
        </div>
      </div>

    </div>
  </div>
</template>
