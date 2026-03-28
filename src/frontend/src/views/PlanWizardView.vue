<template>
  <div class="max-w-screen-xl mx-auto px-6 py-10 animate-fade-in-up">
    <!-- Header -->
    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white mb-8 tracking-tight">
      New Plan Wizard
    </h1>

    <!-- Progress Stepper -->
    <div class="flex items-center gap-4 mb-10 text-sm font-medium">
      <div class="flex items-center gap-2 text-neutral-400">
        <span class="w-6 h-6 rounded flex items-center justify-center border border-neutral-300 dark:border-neutral-700 text-xs">1</span>
        Course
      </div>
      <div class="w-8 h-px bg-neutral-200 dark:bg-neutral-800"></div>
      <div class="flex items-center gap-2 text-neutral-900 dark:text-white">
        <span class="w-6 h-6 rounded flex items-center justify-center bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 text-xs">2</span>
        Plan Info
      </div>
      <div class="w-8 h-px bg-neutral-200 dark:bg-neutral-800"></div>
      <div class="flex items-center gap-2 text-neutral-400 hidden sm:flex">
        <span class="w-6 h-6 rounded flex items-center justify-center border border-neutral-300 dark:border-neutral-700 text-xs">3</span>
        Modules
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 simple-card p-6 sm:p-8">
        <h2 class="text-xl font-bold mb-6 text-neutral-900 dark:text-white">
          Plan Configuration
        </h2>

        <div class="space-y-5">
          <div>
            <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Plan Name</label>
            <input
              type="text"
              placeholder="Computer Science - 2024 Revision"
              class="simple-input"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Effective Date</label>
              <input type="date" class="simple-input appearance-none" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Expiration Date</label>
              <input type="date" class="simple-input appearance-none" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Institutional Code (Optional)</label>
            <input
              type="text"
              placeholder="TECH-2024-01"
              class="simple-input font-mono"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Description (Optional)</label>
            <textarea
              placeholder="Brief syllabus summary..."
              rows="3"
              class="simple-input resize-none"
            ></textarea>
          </div>
        </div>

        <div class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-800 flex justify-between">
          <button class="simple-button-outline">
            Back
          </button>
          <button class="simple-button">
            Next Step
          </button>
        </div>
      </div>

      <div class="space-y-6">
        <div class="simple-card p-6">
          <h3 class="font-bold text-sm mb-4 text-neutral-900 dark:text-white">
            Summary
          </h3>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between items-center pb-2 border-b border-neutral-100 dark:border-neutral-800 text-neutral-600 dark:text-neutral-400">
              <span>Course</span>
              <span class="font-medium text-neutral-900 dark:text-white">Applied Math</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-neutral-100 dark:border-neutral-800 text-neutral-600 dark:text-neutral-400">
              <span>Year</span>
              <span class="font-medium text-neutral-900 dark:text-white">2024/2025</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
