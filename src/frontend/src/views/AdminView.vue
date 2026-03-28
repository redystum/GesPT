<template>
  <div class="mx-auto px-6 py-10 max-w-screen-2xl animate-fade-in-up">
    <!-- Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
      <div>
        <h2 class="text-3xl font-bold text-neutral-900 dark:text-white mb-2 tracking-tight">
          Auth Management
        </h2>
        <p class="text-neutral-500 dark:text-neutral-400 text-sm">
          Admin Console
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button class="simple-button-outline">
          Discard
        </button>
        <button class="simple-button">
          Save Configuration
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Sidebar -->
      <aside class="lg:w-64 flex-shrink-0">
        <nav class="space-y-1">
          <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
            Dashboard
          </a>
          <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
            Curriculum Admin
          </a>
          <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-neutral-900 dark:text-white bg-neutral-100 dark:bg-neutral-800 rounded-lg transition-colors">
            Auth Settings
          </a>
          <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-neutral-600 dark:text-neutral-400 rounded-lg hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors">
            Audit Logs
          </a>
        </nav>
      </aside>

      <!-- Panel Area -->
      <div class="flex-grow space-y-6">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div class="xl:col-span-1 space-y-6">
            <!-- Login Validation Config -->
            <div class="simple-card p-6">
              <h3 class="font-bold text-neutral-900 dark:text-white mb-4 text-sm uppercase tracking-widest text-neutral-500">
                Validation Rule
              </h3>
              
              <label class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-2">Regex Pattern</label>
              <input
                type="text"
                value="^[a-zA-Z0-9._%+-]+@university\.edu$"
                class="simple-input font-mono text-xs mb-4"
              />
              
              <div class="bg-neutral-50 dark:bg-neutral-800/50 text-neutral-600 dark:text-neutral-400 p-4 rounded-md text-xs mb-4">
                <span class="font-bold text-neutral-900 dark:text-white">Rule behavior:</span> Only accounts using the @university.edu domain are permitted to authenticate.
              </div>
              
              <button class="simple-button-outline w-full text-xs">
                Test Pattern
              </button>
            </div>

            <!-- System Policies Config -->
            <div class="simple-card p-6">
              <h3 class="font-bold text-neutral-900 dark:text-white mb-4 text-sm uppercase tracking-widest text-neutral-500">
                System Policies
              </h3>
              <div class="space-y-4">
                <div class="flex justify-between items-center pb-4 border-b border-neutral-100 dark:border-neutral-800">
                  <span class="text-sm text-neutral-700 dark:text-neutral-300">MFA Enforcement</span>
                  <span class="px-2 py-1 text-[10px] font-bold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded">ACTIVE</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-neutral-100 dark:border-neutral-800">
                  <span class="text-sm text-neutral-700 dark:text-neutral-300">SAML 2.0 (SSO)</span>
                  <span class="px-2 py-1 text-[10px] font-bold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded">CONNECTED</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-neutral-700 dark:text-neutral-300">Brute Force Prot.</span>
                  <span class="px-2 py-1 text-[10px] font-bold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 rounded">PENDING</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Email Whitelist Data Table -->
          <div class="xl:col-span-2 simple-card flex flex-col overflow-hidden">
            <div class="p-5 border-b border-neutral-200 dark:border-neutral-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
              <div>
                <h3 class="font-bold text-neutral-900 dark:text-white text-base">Access Whitelist</h3>
              </div>
              <div class="flex gap-3 w-full md:w-auto">
                <input type="text" placeholder="Filter rules..." class="simple-input text-xs py-1.5 w-full md:w-48" />
                <button class="simple-button text-xs py-1.5 whitespace-nowrap">
                  Add Entry
                </button>
              </div>
            </div>

            <div class="overflow-x-auto flex-grow">
              <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-xs text-neutral-500 dark:text-neutral-400 border-b border-neutral-200 dark:border-neutral-800">
                  <tr>
                    <th class="px-5 py-3 font-medium">Identifier / Domain</th>
                    <th class="px-5 py-3 font-medium">Role</th>
                    <th class="px-5 py-3 font-medium text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                  <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <td class="px-5 py-4">
                      <div class="font-medium text-neutral-900 dark:text-white">@university.edu</div>
                      <div class="text-xs text-neutral-500 mt-0.5">Verified Domain</div>
                    </td>
                    <td class="px-5 py-4">
                      <span class="bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 px-2 py-1 rounded text-xs">Faculty</span>
                    </td>
                    <td class="px-5 py-4 text-right">
                      <button class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white font-medium text-xs">Edit</button>
                    </td>
                  </tr>
                  <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <td class="px-5 py-4">
                      <div class="font-medium text-neutral-900 dark:text-white">dean@curator.io</div>
                      <div class="text-xs text-neutral-500 mt-0.5">Individual Account</div>
                    </td>
                    <td class="px-5 py-4">
                      <span class="bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 px-2 py-1 rounded text-xs">Admin</span>
                    </td>
                    <td class="px-5 py-4 text-right">
                      <button class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white font-medium text-xs">Edit</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <div class="p-4 border-t border-neutral-200 dark:border-neutral-800 flex justify-between items-center text-xs text-neutral-500">
              <span>Showing 2 of 1,284 entries</span>
              <div class="flex gap-2">
                <button class="px-2 py-1 hover:text-neutral-900 dark:hover:text-white disabled:opacity-50" disabled>Previous</button>
                <button class="px-2 py-1 hover:text-neutral-900 dark:hover:text-white">Next</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
