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
      <router-link to="/admin/vendors" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        Vendors
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Edit Vendor</span>
    </nav>

    <!-- Page Header -->
    <div class="flex items-center gap-4">
      <router-link to="/admin/vendors" class="p-2 rounded-lg transition-colors" :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'" aria-label="Back to vendors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </router-link>
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Edit Vendor</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">Update vendor information and account details</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading vendor information...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" :class="[
      'rounded-xl border-l-4 p-4',
      isDarkMode ? 'bg-red-900/20 border-red-500 text-red-400' : 'bg-red-50 border-red-500 text-red-700'
    ]" role="alert">
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ error }}
      </div>
    </div>

    <!-- Form -->
    <div v-else :class="[
      'rounded-2xl shadow-lg p-6',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <form @submit.prevent="updateVendor" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Business Name -->
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Business Name <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              <input 
                type="text" 
                v-model="form.business_name" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.business_name ? 'border-red-500' : ''
                ]"
                placeholder="Enter business name"
              >
            </div>
            <p v-if="errors.business_name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.business_name }}</p>
          </div>

          <!-- Business Logo -->
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Business Logo
            </label>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
              <!-- Logo Preview -->
              <div 
                @click="$refs.logoInput.click()"
                :class="[
                  'relative w-32 h-32 border-2 border-dashed rounded-xl hover:border-blue-500 transition-colors cursor-pointer flex items-center justify-center overflow-hidden flex-shrink-0',
                  isDarkMode ? 'border-gray-600 hover:border-blue-400 bg-gray-700' : 'border-gray-300 hover:border-blue-500 bg-gray-50',
                  (logoPreview || vendorLogo) ? 'border-blue-500' : ''
                ]"
              >
                <img 
                  v-if="logoPreview || vendorLogo" 
                  :src="logoPreview || vendorLogo" 
                  alt="Logo preview" 
                  class="w-full h-full object-cover"
                >
                <div v-else class="text-center">
                  <svg class="w-10 h-10 mx-auto" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <p class="text-xs mt-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Click to upload</p>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">PNG, JPG, SVG</p>
                </div>
                <input 
                  type="file" 
                  ref="logoInput" 
                  @change="handleLogoUpload" 
                  accept="image/*"
                  class="hidden"
                >
              </div>
              
              <!-- Logo Info -->
              <div class="flex-1">
                <div v-if="logoPreview || vendorLogo" class="flex flex-col">
                  <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                      {{ logoFileName || 'Current Logo' }}
                    </span>
                    <span v-if="logoFileSize" class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">({{ logoFileSize }})</span>
                    <span v-else-if="vendorLogo" class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">(Current)</span>
                  </div>
                  <div class="flex flex-wrap gap-3 mt-2">
                    <button 
                      type="button"
                      @click="changeLogo" 
                      class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                    >
                      <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                      </svg>
                      Change
                    </button>
                    <button 
                      type="button"
                      @click="removeLogo" 
                      class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                    >
                      <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      Remove
                    </button>
                  </div>
                </div>
                <div v-else class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  <p class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Recommended: Square image, max 2MB
                  </p>
                  <p class="mt-1">Supported formats: JPG, PNG, GIF, SVG, WEBP</p>
                </div>
                <p v-if="errors.logo" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.logo }}</p>
              </div>
            </div>
          </div>

          <!-- Vendor Owner Details -->
          <div class="col-span-2 border-b pb-4 mb-2" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
            <h3 class="text-md font-semibold flex items-center" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Account Details
            </h3>
          </div>

          <!-- Name -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Full Name <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <input 
                type="text" 
                v-model="form.name" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.name ? 'border-red-500' : ''
                ]"
                placeholder="Enter full name"
              >
            </div>
            <p v-if="errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.name }}</p>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Email Address <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <input 
                type="email" 
                v-model="form.email" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.email ? 'border-red-500' : ''
                ]"
                placeholder="Enter email address"
              >
            </div>
            <p v-if="errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.email }}</p>
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Phone Number <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              <input 
                type="text" 
                v-model="form.phone" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.phone ? 'border-red-500' : ''
                ]"
                placeholder="Enter phone number"
              >
            </div>
            <p v-if="errors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.phone }}</p>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              New Password
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <input 
                type="password" 
                v-model="form.password" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.password ? 'border-red-500' : ''
                ]"
                placeholder="Enter new password (optional)"
              >
            </div>
            <p class="mt-1 text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Leave blank to keep current password
            </p>
            <p v-if="errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.password }}</p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Confirm Password
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <input 
                type="password" 
                v-model="form.password_confirmation" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.password_confirmation ? 'border-red-500' : ''
                ]"
                placeholder="Confirm new password"
              >
            </div>
            <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.password_confirmation }}</p>
          </div>

          <!-- Vendor Details -->
          <div class="col-span-2 border-b pb-4 mb-2 mt-2" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
            <h3 class="text-md font-semibold flex items-center" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              Vendor Details
            </h3>
          </div>

          <!-- Business Address -->
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Business Address <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-3" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <textarea 
                v-model="form.business_address" 
                rows="2" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-y',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.business_address ? 'border-red-500' : ''
                ]"
                placeholder="Enter business address"
              ></textarea>
            </div>
            <p v-if="errors.business_address" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.business_address }}</p>
          </div>

     

          <!-- Plan Selection -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Plan <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <select 
                v-model="form.plan_id" 
                :class="[
                  'w-full pl-10 pr-10 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900',
                  errors.plan_id ? 'border-red-500' : ''
                ]"
              >
                <option value="">Select a plan</option>
                <option 
                  v-for="plan in plans" 
                  :key="plan.id" 
                  :value="plan.id"
                >
                  {{ plan.name }}
                </option>
              </select>
              <svg class="w-5 h-5 absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <p v-if="errors.plan_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.plan_id }}</p>
            <p class="mt-1 text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Select a subscription plan for this vendor
            </p>
          </div>

          <!-- Commission Rate -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Commission Rate (%) <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
              </svg>
              <input 
                type="number" 
                step="0.01" 
                min="0" 
                max="100"
                v-model="form.commission_rate" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900',
                  errors.commission_rate ? 'border-red-500' : ''
                ]"
                placeholder="Enter commission rate"
              >
            </div>
            <p v-if="errors.commission_rate" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.commission_rate }}</p>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Vendor Status
            </label>
            <div class="flex items-center space-x-6 pt-1">
              <label class="inline-flex items-center cursor-pointer">
                <input 
                  type="radio" 
                  v-model="form.is_active" 
                  :value="true" 
                  class="form-radio"
                  :class="isDarkMode ? 'bg-gray-700 border-gray-500' : 'bg-white border-gray-300'"
                >
                <span class="ml-2 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Active</span>
              </label>
              <label class="inline-flex items-center cursor-pointer">
                <input 
                  type="radio" 
                  v-model="form.is_active" 
                  :value="false" 
                  class="form-radio"
                  :class="isDarkMode ? 'bg-gray-700 border-gray-500' : 'bg-white border-gray-300'"
                >
                <span class="ml-2 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Inactive</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 pt-6 border-t flex flex-wrap items-center justify-end gap-3" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
          <button 
            type="button" 
            @click="goBack" 
            :class="[
              'px-6 py-2.5 rounded-lg transition-colors text-sm font-medium',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Cancel
          </button>
          <button 
            type="submit" 
            :disabled="submitting"
            class="px-6 py-2.5 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md flex items-center text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="submitting" class="mr-2">
              <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            {{ submitting ? 'Updating Vendor...' : 'Update Vendor' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const error = ref(null);
const submitting = ref(false);
const errors = ref({});
const vendorId = ref(null);
const vendorLogo = ref(null);
const plans = ref([]);
const isDarkMode = ref(false);
let darkModeObserver = null;

// Logo related
const logoPreview = ref(null);
const logoFile = ref(null);
const logoFileName = ref('');
const logoFileSize = ref('');

// Form data
const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  business_name: '',
  business_address: '',
  plan_id: '',
  commission_rate: 10.00,
  is_active: true,
  logo: null
});

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

// Fetch plans
const fetchPlans = async () => {
  try {
    const response = await axios.get('/api/admin/plans', {
      params: {
        status: "active"
      }
    });
    plans.value = response.data.data.data || [];
  } catch (error) {
    console.error('Error fetching plans:', error);
  }
};

// Fetch vendor data
const fetchVendor = async () => {
  try {
    loading.value = true;
    error.value = null;
    vendorId.value = route.params.id;
    
    const response = await axios.get(`/api/admin/vendors/${vendorId.value}`);
    const data = response.data;
    
    form.name = data.vendor.user?.name || '';
    form.email = data.vendor.user?.email || '';
    form.phone = data.vendor.user?.phone || '';
    form.business_name = data.vendor.business_name || '';
    form.business_address = data.vendor.business_address || '';
    form.plan_id = data.vendor.plan_id || '';
    form.commission_rate = data.vendor.commission_rate || 10.00;
    form.is_active = data.vendor.is_active ?? true;
    
    if (data.vendor.business_logo) {              
      vendorLogo.value = '/storage/'+data.vendor.business_logo;
      logoFileName.value = data.vendor.business_logo?.split('/storage/').pop() || 'Current Logo';
    }
  } catch (err) {
    console.error('Error fetching vendor:', err);
    error.value = err.response?.data?.message || 'Failed to load vendor data';
  } finally {
    loading.value = false;
  }
};

// Handle logo upload
const handleLogoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
    if (!validTypes.includes(file.type)) {
      showToast('Please upload a valid image file (JPG, PNG, GIF, SVG, WEBP)', 'error');
      event.target.value = '';
      return;
    }
    
    if (file.size > 2 * 1024 * 1024) {
      showToast('File size must be less than 2MB', 'error');
      event.target.value = '';
      return;
    }

    logoFile.value = file;
    logoFileName.value = file.name;
    logoFileSize.value = formatFileSize(file.size);
    form.logo = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
      logoPreview.value = e.target.result;
      vendorLogo.value = null;
    };
    reader.readAsDataURL(file);
  }
};

// Format file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Change logo
const changeLogo = () => {
  document.querySelector('input[type="file"]')?.click();
};

// Remove logo
const removeLogo = () => {
  logoPreview.value = null;
  vendorLogo.value = null;
  logoFile.value = null;
  logoFileName.value = '';
  logoFileSize.value = '';
  form.logo = null;
  const input = document.querySelector('input[type="file"]');
  if (input) input.value = '';
};

// Update vendor
const updateVendor = async () => {
  errors.value = {};
  submitting.value = true;

  try {
    const formData = new FormData();
    
    formData.append('name', form.name);
    formData.append('email', form.email);
    formData.append('phone', form.phone);
    
    if (form.password) {
      formData.append('password', form.password);
      formData.append('password_confirmation', form.password_confirmation || '');
    }
    
    formData.append('business_name', form.business_name);
    formData.append('business_address', form.business_address);
    formData.append('plan_id', form.plan_id || '');
    formData.append('commission_rate', form.commission_rate);
    formData.append('is_active', form.is_active ? '1' : '0');
    
    if (logoFile.value) {
      formData.append('logo', logoFile.value);
    } else if (logoPreview.value === null && vendorLogo.value === null) {
      formData.append('remove_logo', 'true');
    }
    
    formData.append('_method', 'PUT');

    const response = await axios.post(`/api/admin/vendors/${vendorId.value}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    showToast('Vendor updated successfully!', 'success');
    router.push(`/admin/vendors/${vendorId.value}`);
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
      const firstError = document.querySelector('.border-red-500');
      if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    } else {
      showToast(error.response?.data?.message || 'Failed to update vendor', 'error');
    }
  } finally {
    submitting.value = false;
  }
};

// Go back
const goBack = () => {
  router.push('/admin/vendors');
};

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchPlans();
  fetchVendor();

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
.form-radio {
  width: 1.2rem;
  height: 1.2rem;
  border-radius: 50%;
  border: 2px solid #d1d5db;
  transition: all 0.2s;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  background-color: white;
}

.dark .form-radio {
  background-color: #1f2937;
  border-color: #4b5563;
}

.form-radio:checked {
  border-color: #3b82f6;
  background-color: #3b82f6;
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
  background-size: 100% 100%;
  background-position: center;
  background-repeat: no-repeat;
}

.dark .form-radio:checked {
  background-color: #3b82f6;
  border-color: #3b82f6;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #3b82f6;
  outline: none;
}

button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.border-red-500 {
  border-color: #ef4444 !important;
}

.border-red-500:focus {
  border-color: #ef4444 !important;
  ring-color: #ef4444 !important;
}

/* Custom scrollbar for dark mode */
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