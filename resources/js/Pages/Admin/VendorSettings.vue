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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Vendor Settings</span>
    </nav>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Vendor Settings</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Manage your business profile and preferences</p>
      </div>
      <button 
        @click="resetSettings" 
        :class="[
          'inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium',
          isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
        :disabled="loading"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Reset
      </button>
    </div>

    <!-- Success Message -->
    <div v-if="successMessage" :class="[
      'rounded-xl px-4 py-3 flex items-center gap-3 animate-slide-up border',
      isDarkMode ? 'bg-green-900/20 border-green-800 text-green-400' : 'bg-green-50 border-green-200 text-green-700'
    ]">
      <svg class="w-5 h-5 flex-shrink-0" :class="isDarkMode ? 'text-green-400' : 'text-green-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span>{{ successMessage }}</span>
      <button @click="successMessage = ''" class="ml-auto" :class="isDarkMode ? 'text-green-400 hover:text-green-300' : 'text-green-700 hover:text-green-900'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form @submit.prevent="updateSettings" enctype="multipart/form-data" class="space-y-6">
      <!-- Business Information Card -->
      <div :class="[
        'rounded-2xl shadow-lg overflow-hidden',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="px-6 py-4 border-b" :class="isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50/50'">
          <h2 class="text-lg font-semibold flex items-center gap-2" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Business Information
          </h2>
          <p class="text-sm mt-0.5" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Update your business profile details</p>
        </div>

        <div class="p-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Business Logo - Full Width -->
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Business Logo</label>
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="flex-shrink-0">
                  <div v-if="settings.business_logo" class="w-24 h-24 rounded-xl border-2 overflow-hidden" :class="isDarkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-200 bg-gray-50'">
                    <img :src="getLogoUrl(settings.business_logo)" alt="Business Logo" class="w-full h-full object-cover">
                  </div>
                  <div v-else class="w-24 h-24 rounded-xl border-2 border-dashed flex items-center justify-center" :class="isDarkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-300 bg-gray-50'">
                    <svg class="w-12 h-12" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <div class="flex flex-col gap-2">
                    <label class="cursor-pointer">
                      <span class="px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-block text-sm font-medium">
                        Choose Logo
                      </span>
                      <input type="file" @change="handleLogoUpload" accept="image/*" class="hidden">
                    </label>
                    <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Recommended: Square image, max 2MB. JPG, PNG, GIF, SVG</p>
                    <p v-if="errors.logo" class="text-xs text-red-600 dark:text-red-400">{{ errors.logo }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Business Name -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Business Name <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="settings.business_name" 
                @blur="validateField('business_name')"
                :class="[
                  'w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.business_name ? 'border-red-500' : ''
                ]"
                placeholder="Enter your business name"
                required
              >
              <p v-if="errors.business_name" class="text-xs text-red-600 dark:text-red-400 mt-1">{{ errors.business_name }}</p>
            </div>

            <!-- Business Email -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Business Email <span class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">(Optional)</span>
              </label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input 
                  type="email" 
                  v-model="settings.business_email" 
                  @blur="validateField('business_email')"
                  :class="[
                    'w-full border rounded-lg pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                    errors.business_email ? 'border-red-500' : ''
                  ]"
                  placeholder="business@example.com"
                >
              </div>
              <p v-if="errors.business_email" class="text-xs text-red-600 dark:text-red-400 mt-1">{{ errors.business_email }}</p>
            </div>

            <!-- Business Phone -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Business Phone <span class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">(Optional)</span>
              </label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <input 
                  type="tel" 
                  v-model="settings.business_phone" 
                  :class="[
                    'w-full border rounded-lg pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                  ]"
                  placeholder="+1 (555) 000-0000"
                >
              </div>
            </div>

            <!-- Business Website -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Business Website <span class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">(Optional)</span>
              </label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                </svg>
                <input 
                  type="url" 
                  v-model="settings.business_website" 
                  @blur="validateField('business_website')"
                  :class="[
                    'w-full border rounded-lg pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                    errors.business_website ? 'border-red-500' : ''
                  ]"
                  placeholder="https://example.com"
                >
              </div>
              <p v-if="errors.business_website" class="text-xs text-red-600 dark:text-red-400 mt-1">{{ errors.business_website }}</p>
            </div>

            <!-- Business Address - Full Width -->
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Business Address <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <svg class="absolute left-3 top-3 w-4 h-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <textarea 
                  v-model="settings.business_address" 
                  @blur="validateField('business_address')"
                  :class="[
                    'w-full border rounded-lg pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                    errors.business_address ? 'border-red-500' : ''
                  ]"
                  rows="3"
                  placeholder="Enter your full business address"
                  required
                ></textarea>
              </div>
              <p v-if="errors.business_address" class="text-xs text-red-600 dark:text-red-400 mt-1">{{ errors.business_address }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Financial Settings Card -->
      <div :class="[
        'rounded-2xl shadow-lg overflow-hidden',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="px-6 py-4 border-b" :class="isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-100 bg-gray-50/50'">
          <h2 class="text-lg font-semibold flex items-center gap-2" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Financial Settings
          </h2>
          <p class="text-sm mt-0.5" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Configure tax and currency preferences</p>
        </div>

        <div class="p-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Registration Number -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Registration Number <span class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">(Optional)</span>
              </label>
              <input 
                type="text" 
                v-model="settings.registration_number" 
                :class="[
                  'w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="Company Registration Number"
              >
            </div>

            <!-- Currency -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Currency <span class="text-red-500">*</span>
              </label>
              <select v-model="settings.currency" :class="[
                'w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]">
                <option value="USD">🇺🇸 USD - US Dollar</option>
                <option value="EUR">🇪🇺 EUR - Euro</option>
                <option value="GBP">🇬🇧 GBP - British Pound</option>
                <option value="CAD">🇨🇦 CAD - Canadian Dollar</option>
                <option value="AUD">🇦🇺 AUD - Australian Dollar</option>
                <option value="JPY">🇯🇵 JPY - Japanese Yen</option>
                <option value="INR">🇮🇳 INR - Indian Rupee</option>
                <option value="PKR">🇵🇰 PKR - Pakistani Rupee</option>
              </select>
            </div>

            <!-- Tax Rate -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Tax Rate <span class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">(Optional)</span>
              </label>
              <div class="flex gap-2">
                <div class="flex-1">
                  <input 
                    type="number" 
                    step="0.01" 
                    v-model="settings.tax_rate" 
                    :class="[
                      'w-full border rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                      isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900',
                      errors.tax_rate ? 'border-red-500' : ''
                    ]"
                    min="0"
                    placeholder="0.00"
                  >
                </div>
                <div class="w-28">
                  <select v-model="settings.tax_type" :class="[
                    'w-full border rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                  ]">
                    <option value="percentage">%</option>
                    <option value="fixed">Fixed</option>
                  </select>
                </div>
              </div>
              <p v-if="errors.tax_rate" class="text-xs text-red-600 dark:text-red-400 mt-1">{{ errors.tax_rate }}</p>
              <p class="text-xs mt-1" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                {{ settings.tax_type === 'percentage' ? 'Percentage of order total' : 'Fixed amount per order' }}
              </p>
            </div>

            <!-- Commission Rate (Read Only) -->
            <div>
              <label class="block text-sm font-medium mb-1.5" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Commission Rate <span class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">(Platform Set)</span>
              </label>
              <div class="flex gap-2">
                <div class="flex-1">
                  <input 
                    type="number" 
                    step="0.01" 
                    v-model="settings.commission_rate" 
                    :class="[
                      'w-full border rounded-lg px-4 py-2.5 cursor-not-allowed',
                      isDarkMode ? 'bg-gray-700/50 border-gray-600 text-gray-400' : 'bg-gray-50 border-gray-200 text-gray-500'
                    ]"
                    min="0"
                    readonly
                    disabled
                  >
                </div>
                <div class="w-28">
                  <select v-model="settings.commission_type" :class="[
                    'w-full border rounded-lg px-3 py-2.5 cursor-not-allowed',
                    isDarkMode ? 'bg-gray-700/50 border-gray-600 text-gray-400' : 'bg-gray-50 border-gray-200 text-gray-500'
                  ]" disabled>
                    <option value="percentage">%</option>
                    <option value="fixed">Fixed</option>
                  </select>
                </div>
              </div>
              <p class="text-xs mt-1" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Commission rate is configured by the platform administrator
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit Actions -->
      <div :class="[
        'rounded-2xl shadow-lg p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex flex-col sm:flex-row justify-end gap-3">
          <button 
            type="button" 
            @click="resetSettings" 
            :class="[
              'px-6 py-2.5 rounded-lg transition-colors text-sm font-medium',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
            :disabled="loading"
          >
            Cancel
          </button>
          <button 
            type="submit" 
            class="px-6 py-2.5 bg-blue-500 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md flex items-center justify-center gap-2 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="loading"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="loading">Saving...</span>
            <span v-else>
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
              </svg>
              Save Settings
            </span>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const settings = ref({
  business_name: '',
  business_email: '',
  business_phone: '',
  business_website: '',
  business_address: '',
  tax_type: 'percentage',
  tax_rate: 0,
  commission_type: 'percentage',
  commission_rate: 0,
  registration_number: '',
  currency: 'USD',
  business_logo: null
});

const errors = ref({
  business_name: '',
  business_email: '',
  business_website: '',
  business_address: '',
  tax_rate: '',
  commission_rate: '',
  logo: ''
});

const loading = ref(false);
const successMessage = ref('');
const logoFile = ref(null);
const isDarkMode = ref(false);
let darkModeObserver = null;

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

const getLogoUrl = (filename) => {
  if (!filename) return '';
  if (filename.startsWith('http')) return filename;
  if (filename.startsWith('data:')) return filename;
  return `/storage/${filename}`;
};

const validateField = (field) => {
  switch(field) {
    case 'business_name':
      if (!settings.value.business_name || settings.value.business_name.trim() === '') {
        errors.value.business_name = 'Business name is required';
      } else {
        errors.value.business_name = '';
      }
      break;
      
    case 'business_email':
      if (settings.value.business_email && !isValidEmail(settings.value.business_email)) {
        errors.value.business_email = 'Please enter a valid email address';
      } else {
        errors.value.business_email = '';
      }
      break;
      
    case 'business_website':
      if (settings.value.business_website && !isValidUrl(settings.value.business_website)) {
        errors.value.business_website = 'Please enter a valid URL (e.g., https://example.com)';
      } else {
        errors.value.business_website = '';
      }
      break;
      
    case 'business_address':
      if (!settings.value.business_address || settings.value.business_address.trim() === '') {
        errors.value.business_address = 'Business address is required';
      } else {
        errors.value.business_address = '';
      }
      break;
      
    case 'tax_rate':
      if (settings.value.tax_rate < 0) {
        errors.value.tax_rate = 'Tax rate cannot be negative';
      } else {
        errors.value.tax_rate = '';
      }
      break;
  }
};

const isValidEmail = (email) => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
};

const isValidUrl = (url) => {
  try {
    new URL(url);
    return true;
  } catch {
    return false;
  }
};

const validateAll = () => {
  let isValid = true;
  
  if (!settings.value.business_name || settings.value.business_name.trim() === '') {
    errors.value.business_name = 'Business name is required';
    isValid = false;
  }
  
  if (!settings.value.business_address || settings.value.business_address.trim() === '') {
    errors.value.business_address = 'Business address is required';
    isValid = false;
  }
  
  if (settings.value.business_email && !isValidEmail(settings.value.business_email)) {
    errors.value.business_email = 'Please enter a valid email address';
    isValid = false;
  }
  
  if (settings.value.business_website && !isValidUrl(settings.value.business_website)) {
    errors.value.business_website = 'Please enter a valid URL (e.g., https://example.com)';
    isValid = false;
  }
  
  if (settings.value.tax_rate < 0) {
    errors.value.tax_rate = 'Tax rate cannot be negative';
    isValid = false;
  }
  
  return isValid;
};

const fetchSettings = async () => {
  try {
    const response = await axios.get('/api/admin/vendor/settings');
    settings.value = {
      ...settings.value,
      ...response.data
    };
  } catch (error) {
    console.error('Error fetching settings:', error);
    showToast('Failed to load settings', 'error');
  }
};

const handleLogoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      errors.value.logo = 'Logo file size must be less than 2MB';
      event.target.value = '';
      return;
    }
    
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
    if (!allowedTypes.includes(file.type)) {
      errors.value.logo = 'Please upload a valid image (JPG, PNG, GIF, SVG)';
      event.target.value = '';
      return;
    }
    
    errors.value.logo = '';
    logoFile.value = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
      settings.value.business_logo = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const updateSettings = async () => {
  // Clear previous errors
  errors.value = {
    business_name: '',
    business_email: '',
    business_website: '',
    business_address: '',
    tax_rate: '',
    commission_rate: '',
    logo: ''
  };
  
  if (!validateAll()) {
    const firstError = document.querySelector('.border-red-500');
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstError.focus();
    }
    return;
  }
  
  loading.value = true;
  successMessage.value = '';
  
  try {
    const formData = new FormData();
    
    formData.append('business_name', settings.value.business_name || '');
    formData.append('business_email', settings.value.business_email || '');
    formData.append('business_phone', settings.value.business_phone || '');
    formData.append('business_website', settings.value.business_website || '');
    formData.append('business_address', settings.value.business_address || '');
    formData.append('tax_type', settings.value.tax_type || 'percentage');
    formData.append('tax_rate', settings.value.tax_rate || 0);
    formData.append('commission_type', settings.value.commission_type || 'percentage');
    formData.append('commission_rate', settings.value.commission_rate || 0);
    formData.append('registration_number', settings.value.registration_number || '');
    formData.append('currency', settings.value.currency || 'USD');
    
    if (logoFile.value) {
      formData.append('business_logo', logoFile.value);
    }
    
    const response = await axios.post('/api/admin/vendor/settings', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'X-HTTP-Method-Override': 'PUT'
      }
    });
    
    if (response.data.business_logo) {
      settings.value.business_logo = response.data.business_logo;
      logoFile.value = null;
    }
    
    successMessage.value = 'Settings updated successfully!';
    showToast('Settings updated successfully!', 'success');
    
    setTimeout(() => {
      successMessage.value = '';
    }, 5000);
  } catch (error) {
    console.error('Error updating settings:', error);
    
    if (error.response?.data?.errors) {
      const serverErrors = error.response.data.errors;
      Object.keys(serverErrors).forEach(key => {
        if (errors.value.hasOwnProperty(key)) {
          errors.value[key] = serverErrors[key][0];
        }
      });
      showToast('Please fix the validation errors', 'error');
    } else {
      showToast(error.response?.data?.message || 'Failed to update settings. Please try again.', 'error');
    }
  } finally {
    loading.value = false;
  }
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

const resetSettings = () => {
  if (confirm('Are you sure you want to reset all changes?')) {
    logoFile.value = null;
    errors.value = {
      business_name: '',
      business_email: '',
      business_website: '',
      business_address: '',
      tax_rate: '',
      commission_rate: '',
      logo: ''
    };
    fetchSettings();
  }
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
});

onUnmounted(() => {
  if (darkModeObserver) {
    darkModeObserver.disconnect();
  }
});
</script>

<style scoped>
.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(-10px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
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

/* Remove number input arrows */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}

/* Dark mode scrollbar */
.dark ::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.dark ::-webkit-scrollbar-track {
  background: #1f2937;
}

.dark ::-webkit-scrollbar-thumb {
  background: #4b5563;
  border-radius: 4px;
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>