<!-- resources/js/views/admin/SettingsManager.vue -->
<template>
  <div class="space-y-6 pb-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
      <router-link to="/admin/dashboard" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Settings</span>
    </nav>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">System Settings</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Manage application configuration</p>
      </div>
      <button @click="openCreateModal" 
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Add Setting</span>
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Settings</p>
            <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ filteredSettings.length }}</p>
          </div>
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
        </div>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Active</p>
            <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ activeCount }}</p>
          </div>
          <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-gray-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Inactive</p>
            <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ inactiveCount }}</p>
          </div>
          <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
        </div>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Logo</p>
            <div class="stat-value" v-if="logoSetting">
              <img :src="getLogoUrl(logoSetting.value)" alt="Logo" class="stat-logo-preview" />
            </div>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" v-else>No logo set</p>
          </div>
          <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="flex items-center gap-4">
      <div class="flex-1 relative">
        <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
          <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"/>
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search settings..."
          :class="[
            'w-full pl-10 pr-4 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition',
            isDarkMode ? 'bg-gray-800 border-gray-700 text-white placeholder-gray-400' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-500'
          ]"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        <p class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading settings...</p>
      </div>
    </div>

    <!-- Settings Grid -->
    <div v-else-if="filteredSettings.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="setting in filteredSettings" :key="setting.id || setting.key" 
        :class="[
          'rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden',
          isDarkMode ? 'bg-gray-800 hover:shadow-gray-700' : 'bg-white'
        ]">
        <div class="p-5">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3">
                <code class="px-2.5 py-1 text-xs font-semibold rounded-lg" :class="isDarkMode ? 'bg-gray-700 text-gray-200' : 'bg-gray-100 text-gray-700'">
                  {{ setting.key.charAt(0).toUpperCase() + setting.key.slice(1) }}
                </code>
              </div>
              <div class="mt-2 flex items-center gap-2">
                <template v-if="setting.key === 'logo' && setting.value">
                  <img :src="getLogoUrl(setting.value)" alt="Logo" class="h-8 w-auto object-contain rounded border" :class="isDarkMode ? 'border-gray-600' : 'border-gray-200'" />
                </template>
                <span class="text-sm font-mono" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ setting.value }}</span>
                <button
                  class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition"
                  @click="copyToClipboard(setting.value)"
                  title="Copy value"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </button>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button @click="openEditModal(setting)" 
                class="p-1.5 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button @click="confirmDelete(setting)" 
                class="p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      <h3 class="text-lg font-semibold mb-2" :class="isDarkMode ? 'text-white' : 'text-gray-900'">No Settings Found</h3>
      <p class="mb-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Get started by creating your first setting.</p>
      <button @click="openCreateModal" 
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Setting
      </button>
    </div>

    <!-- Settings Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
      <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
        <div :class="[
          'rounded-2xl shadow-2xl max-w-md w-full z-50 transform transition-all',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 rounded-t-2xl" 
            :class="[
              isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-white'
            ]">
            <h3 class="text-xl font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ modalTitle }}</h3>
            <button @click="closeModal" class="transition" :class="isDarkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-400 hover:text-gray-600'">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="saveSetting" class="px-6 py-4">
            <div class="space-y-4">
              <!-- Key -->
              <div>
                <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Key *</label>
                <input type="text" v-model="form.key" required 
                  :disabled="isEditMode"
                  :class="[
                    'w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                    isEditMode ? 'opacity-60 cursor-not-allowed' : ''
                  ]"
                  placeholder="e.g., site_name">
                <small v-if="isEditMode" class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Key cannot be changed</small>
              </div>

              <!-- Logo Upload -->
              <div v-if="form.key === 'logo'">
                <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Upload Logo</label>
                <div class="logo-upload-area" @dragover.prevent @drop.prevent="handleFileDrop" 
                  :class="isDarkMode ? 'border-gray-600 hover:bg-gray-700' : 'border-gray-200 hover:bg-gray-50'">
                  <input type="file" id="logoUpload" accept="image/*" @change="handleLogoUpload" class="file-input" />
                  <div class="upload-preview" v-if="form.logoPreview || form.value">
                    <img :src="form.logoPreview || getLogoUrl(form.value)" alt="Logo preview" class="upload-preview-img" />
                    <button type="button" class="remove-logo" @click="removeLogo">×</button>
                  </div>
                  <div class="upload-placeholder" v-else>
                    <svg class="w-12 h-12 mx-auto mb-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">Drop logo here or click to upload</p>
                    <small :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">PNG, JPG, SVG up to 2MB</small>
                  </div>
                </div>
                <div v-if="uploadProgress > 0" class="mt-2 w-full h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full bg-blue-500 transition-all duration-300 rounded-full" :style="{ width: uploadProgress + '%' }"></div>
                </div>
              </div>

              <!-- Value -->
              <div v-else>
                <label class="block text-sm font-medium mb-1" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Value *</label>
                <input type="text" v-model="form.value" required 
                  :class="[
                    'w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                  ]"
                  placeholder="Enter setting value">
              </div>

              <!-- Status Toggle -->
              <div :class="[
                'flex items-center justify-between p-3 rounded-xl',
                isDarkMode ? 'bg-gray-700' : 'bg-gray-50'
              ]">
                <label class="text-sm font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Status</label>
                <button type="button" @click="form.is_active = !form.is_active"
                  class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  :class="form.is_active ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'">
                  <span class="sr-only">Toggle status</span>
                  <span aria-hidden="true" 
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    :class="form.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
              </div>
            </div>
            
            <!-- Form Actions -->
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
              <button type="button" @click="closeModal" 
                :class="[
                  'px-4 py-2 border rounded-lg transition',
                  isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                ]">
                Cancel
              </button>
              <button type="submit" :disabled="saving"
                class="px-4 py-2 bg-blue-500 from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="saving" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
                {{ isEditMode ? 'Update Setting' : 'Create Setting' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 overflow-y-auto" @click.self="cancelDelete">
      <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
        <div :class="[
          'rounded-2xl shadow-2xl max-w-md w-full z-50 transform transition-all',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 rounded-t-2xl" 
            :class="[
              isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-white'
            ]">
            <div class="flex items-center gap-2">
              <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              <h3 class="text-xl font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Confirm Delete</h3>
            </div>
            <button @click="cancelDelete" class="transition" :class="isDarkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-400 hover:text-gray-600'">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="px-6 py-4">
            <p class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Are you sure you want to delete the setting <strong>{{ deleteTarget?.key }}</strong>?
            </p>
            <p v-if="deleteTarget?.key === 'logo'" class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">
              ⚠️ This will remove the logo image from the system.
            </p>
            <p class="mt-1 text-sm text-red-500">This action cannot be undone.</p>
            
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
              <button type="button" @click="cancelDelete" 
                :class="[
                  'px-4 py-2 border rounded-lg transition',
                  isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                ]">
                Cancel
              </button>
              <button type="button" @click="deleteSetting" :disabled="deleting"
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="deleting" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<!-- resources/js/views/admin/SettingsManager.vue -->
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const settings = ref([]);
const searchQuery = ref('');
const showModal = ref(false);
const isEditMode = ref(false);
const showDeleteConfirm = ref(false);
const deleteTarget = ref(null);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const uploadProgress = ref(0);
const isDarkMode = ref(false);
let darkModeObserver = null;

const form = ref({
  key: '',
  value: '',
  is_active: true,
  logoFile: null,
  logoPreview: null,
});
const editKey = ref(null);

// Check dark mode from localStorage or system preference
const checkDarkMode = () => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    isDarkMode.value = true;
  } else if (savedTheme === 'light') {
    isDarkMode.value = false;
  } else {
    isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
  }
};

// Watch for theme changes using MutationObserver
const setupDarkModeWatcher = () => {
  const htmlElement = document.documentElement;
  darkModeObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.attributeName === 'class') {
        const hasDarkClass = htmlElement.classList.contains('dark');
        if (hasDarkClass !== isDarkMode.value) {
          isDarkMode.value = hasDarkClass;
        }
      }
    });
  });
  darkModeObserver.observe(htmlElement, { attributes: true });
};

const filteredSettings = computed(() => {
  if (!searchQuery.value) return settings.value;
  const query = searchQuery.value.toLowerCase();
  return settings.value.filter(
    s => s.key.toLowerCase().includes(query) ||
         s.value.toLowerCase().includes(query)
  );
});

const activeCount = computed(() => {
  return settings.value.filter(s => s.is_active).length;
});

const inactiveCount = computed(() => {
  return settings.value.filter(s => !s.is_active).length;
});

const modalTitle = computed(() => {
  return isEditMode.value ? 'Edit Setting' : 'Create New Setting';
});

const logoSetting = computed(() => {
  return settings.value.find(s => s.key === 'logo');
});

const getFullLogoUrl = (filename) => {
  if (!filename) return '';
  if (filename.startsWith('http')) return filename;
  let cleanFilename = filename.replace(/^\/+/, '');
  if (cleanFilename.includes('storage/company-logos/')) {
    cleanFilename = cleanFilename.split('storage/company-logos/').pop();
  }
  return `${window.location.origin}/storage/company-logos/${cleanFilename}`;
};

const getLogoUrl = (filename) => {
  if (!filename) return '';
  if (filename.startsWith('http')) return filename;
  return `/storage/company-logos/${filename}`;
};

// Update logo in localStorage with full URL
 // In SettingsManager.vue, update the updateLogoInStorage function:
const updateLogoInStorage = (logoUrl) => {
  if (logoUrl) {
    // Store the full URL
    const fullUrl = logoUrl.startsWith('http') ? logoUrl : getFullLogoUrl(logoUrl);
    localStorage.setItem('logo', fullUrl);
  } else {
    localStorage.removeItem('logo');
  }
  // Dispatch event to notify other tabs/components
  window.dispatchEvent(new CustomEvent('logo-updated', { detail: { url: logoUrl } }));
  // Also trigger storage event for cross-tab communication
  window.dispatchEvent(new StorageEvent('storage', {
    key: 'logo',
    newValue: logoUrl || null,
    oldValue: null,
    storageArea: localStorage
  }));
};
const fetchSettings = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/settings');
    settings.value = response.data;
    
    // If logo exists, update localStorage with full URL
    const logoSetting = settings.value.find(s => s.key === 'logo');
    if (logoSetting && logoSetting.value) {
      const fullUrl = getFullLogoUrl(logoSetting.value);
      localStorage.setItem('logo', fullUrl);
    }
  } catch (error) {
    console.error('Error fetching settings:', error);
    // Fallback to mock data
    settings.value = [
      { id: 1, key: 'site_name', value: 'My App', is_active: true },
      { id: 2, key: 'logo', value: 'logo.png', is_active: true },
      { id: 3, key: 'maintenance_mode', value: 'false', is_active: false },
      { id: 4, key: 'api_timeout', value: '30', is_active: true },
      { id: 5, key: 'default_language', value: 'en', is_active: true },
    ];
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  isEditMode.value = false;
  editKey.value = null;
  form.value = {
    key: '',
    value: '',
    is_active: true,
    logoFile: null,
    logoPreview: null,
  };
  showModal.value = true;
};

const openEditModal = (setting) => {
  isEditMode.value = true;
  editKey.value = setting.key;
  form.value = {
    key: setting.key,
    value: setting.value,
    is_active: setting.is_active,
    logoFile: null,
    logoPreview: null,
  };
  
  if (setting.key === 'logo' && setting.value) {
    form.value.logoPreview = getLogoUrl(setting.value);
  }
  
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  isEditMode.value = false;
  editKey.value = null;
  form.value.logoFile = null;
  form.value.logoPreview = null;
  uploadProgress.value = 0;
};

const saveSetting = async () => {
  saving.value = true;
  try {
    let value = form.value.value;
    let fullLogoUrl = null;
    
    // Handle logo upload
    if (form.value.key === 'logo' && form.value.logoFile) {
      const formData = new FormData();
      formData.append('logo', form.value.logoFile);
      
      const uploadResponse = await axios.post('/api/admin/settings/logo', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onUploadProgress: (progressEvent) => {
          uploadProgress.value = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total
          );
        },
      });
      
      value = uploadResponse.data.filename;
      // Get the full URL from the response
      fullLogoUrl = uploadResponse.data.url || getFullLogoUrl(value);
    }

    const settingData = {
      key: form.value.key,
      value: value,
      is_active: form.value.is_active,
    };

    let response;
    if (isEditMode.value) {
      response = await axios.put(`/api/admin/settings/${editKey.value}`, settingData);
      const index = settings.value.findIndex(s => s.key === editKey.value);
      if (index !== -1) {
        settings.value[index] = response.data.data;
      }
    } else {
      response = await axios.post('/api/admin/settings', settingData);
      settings.value.push(response.data.data);
    }

    // Update logo in localStorage if logo was updated
    if (form.value.key === 'logo') {
      const logoUrl = fullLogoUrl || getFullLogoUrl(value);
      updateLogoInStorage(logoUrl);
    }

    closeModal();
    showToast('Setting saved successfully!', 'success');
  } catch (error) {
    console.error('Error saving setting:', error);
    showToast('Failed to save setting. Please try again.', 'error');
  } finally {
    saving.value = false;
  }
};

const toggleStatus = async (setting) => {
  try {
    const newStatus = !setting.is_active;
    const response = await axios.patch(`/api/admin/settings/${setting.key}/toggle`, {
      is_active: newStatus
    });
    setting.is_active = response.data.is_active;
    showToast(`Setting ${setting.is_active ? 'activated' : 'deactivated'} successfully!`, 'success');
  } catch (error) {
    console.error('Error toggling status:', error);
    setting.is_active = !setting.is_active;
    showToast('Failed to update status. Please try again.', 'error');
  }
};

const confirmDelete = (setting) => {
  deleteTarget.value = setting;
  showDeleteConfirm.value = true;
};

const cancelDelete = () => {
  showDeleteConfirm.value = false;
  deleteTarget.value = null;
};

const deleteSetting = async () => {
  if (!deleteTarget.value) return;
  
  deleting.value = true;
  try {
    const key = deleteTarget.value.key;
    
    if (key === 'logo' && deleteTarget.value.value) {
      try {
        await axios.delete(`/api/admin/settings/logo/${deleteTarget.value.value}`);
        // Remove logo from localStorage when deleted
        updateLogoInStorage(null);
      } catch (error) {
        console.error('Error deleting logo file:', error);
      }
    }
    
    await axios.delete(`/api/admin/settings/${key}`);
    settings.value = settings.value.filter(s => s.key !== key);
    cancelDelete();
    showToast('Setting deleted successfully!', 'success');
  } catch (error) {
    console.error('Error deleting setting:', error);
    showToast('Failed to delete setting. Please try again.', 'error');
  } finally {
    deleting.value = false;
  }
};

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    showToast('Copied to clipboard!', 'success');
  }).catch(() => {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    showToast('Copied to clipboard!', 'success');
  });
};

const showToast = (message, type = 'info') => {
  const toast = document.createElement('div');
  const bgColor = type === 'success' ? '#16a34a' : type === 'error' ? '#dc2626' : '#3b82f6';
  toast.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 12px 24px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 9999;
    animation: slideIn 0.3s ease;
    background: ${bgColor};
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  `;
  toast.textContent = message;
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(20px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 300);
  }, 3000);
};

// Logo upload methods
const handleLogoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    processLogoFile(file);
  }
};

const handleFileDrop = (event) => {
  const file = event.dataTransfer.files[0];
  if (file) {
    processLogoFile(file);
  }
};

const processLogoFile = (file) => {
  const allowedTypes = ['image/png', 'image/jpeg', 'image/svg+xml', 'image/gif', 'image/webp'];
  if (!allowedTypes.includes(file.type)) {
    alert('Please upload a valid image file (PNG, JPG, SVG, GIF, WebP)');
    return;
  }

  if (file.size > 2 * 1024 * 1024) {
    alert('File size must be less than 2MB');
    return;
  }

  form.value.logoFile = file;
  const reader = new FileReader();
  reader.onload = (e) => {
    form.value.logoPreview = e.target.result;
    form.value.value = file.name;
  };
  reader.readAsDataURL(file);
};

const removeLogo = () => {
  form.value.logoFile = null;
  form.value.logoPreview = null;
  form.value.value = '';
  const fileInput = document.getElementById('logoUpload');
  if (fileInput) fileInput.value = '';
};

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchSettings();

  // Listen for storage changes from other tabs
  window.addEventListener('storage', (e) => {
    if (e.key === 'theme') {
      isDarkMode.value = e.newValue === 'dark';
    }
  });

  // Listen for custom logo update events
  window.addEventListener('logo-updated', (e) => {
    // Refresh settings to get updated logo
    fetchSettings();
  });
});

onUnmounted(() => {
  if (darkModeObserver) {
    darkModeObserver.disconnect();
  }
});
</script>

<style scoped>
.logo-upload-area {
  border: 2px dashed;
  border-radius: 8px;
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
  min-height: 150px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-upload-area:not(.dark) {
  border-color: #e2e8f0;
}

.logo-upload-area.dark {
  border-color: #334155;
}

.logo-upload-area:hover {
  border-color: #3b82f6;
}

.file-input {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  opacity: 0;
  cursor: pointer;
}

.upload-preview {
  position: relative;
  display: inline-block;
}

.upload-preview-img {
  max-height: 120px;
  max-width: 200px;
  object-fit: contain;
  border-radius: 4px;
}

.remove-logo {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  font-size: 16px;
  line-height: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.remove-logo:hover {
  background: #dc2626;
  transform: scale(1.1);
}

.upload-placeholder svg {
  margin-bottom: 0.5rem;
}

.upload-placeholder p {
  margin: 0.25rem 0;
  font-size: 0.9rem;
}

.upload-placeholder small {
  font-size: 0.75rem;
}

.stat-logo-preview {
  height: 30px;
  width: auto;
  object-fit: contain;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

 