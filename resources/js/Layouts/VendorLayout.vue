<!-- resources/js/layouts/VendorLayout.vue -->
<template>
  <div :class="['min-h-screen transition-colors duration-300', isDarkMode ? 'dark bg-gray-900' : 'bg-gray-50']">
    <!-- Subscription Expired Warning Banner -->
    <div v-if="!isSubscriptionValid && !isSubscriptionPage" 
         class="fixed top-16 left-0 right-0 z-40 bg-red-500 text-white px-4 py-2 text-center">
      <div class="flex items-center justify-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="text-sm font-medium">{{ subscriptionMessage || 'Your subscription has expired. Please renew to continue.' }}</span>
        <router-link to="/vendor/subscription" class="text-sm font-semibold underline hover:no-underline">
          Renew Now →
        </router-link>
      </div>
    </div>

    <!-- Trial Expiring Soon Banner -->
    <div v-if="isTrialExpiringSoon && !isSubscriptionPage" 
         class="fixed top-16 left-0 right-0 z-40 bg-yellow-500 text-white px-4 py-2 text-center">
      <div class="flex items-center justify-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="text-sm font-medium">Your trial ends in {{ trialDaysLeft }} days. Subscribe to continue using all features.</span>
        <router-link to="/vendor/subscription" class="text-sm font-semibold underline hover:no-underline">
          Subscribe Now →
        </router-link>
      </div>
    </div>

    <!-- Top Navigation Bar -->
    <nav
      class="fixed w-full z-30 top-0 p-0 transition-all duration-300"
      :class="[
        isDarkMode 
          ? 'bg-gray-800/90 backdrop-blur-md border-b border-gray-700' 
          : 'bg-white/90 backdrop-blur-md border-b border-gray-200 shadow-sm'
      ]"
    >
      <div class="px-3 py-3 lg:py-4 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center justify-start">
            <!-- Mobile menu button -->
            <button 
              @click="toggleSidebar" 
              class="inline-flex items-center p-2 text-sm rounded-lg lg:hidden focus:outline-none focus:ring-2"
              :class="[
                isDarkMode 
                  ? 'text-gray-300 hover:bg-gray-700 focus:ring-gray-600' 
                  : 'text-gray-600 hover:bg-gray-100 focus:ring-gray-200'
              ]"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
            </button>
            
            <!-- Logo -->
            <router-link to="/vendor/dashboard" class="flex items-center ml-2 md:mr-24">
              <img 
                v-if="logoUrl && logoUrl.includes('/storage/vendor-logos/')" 
                :src="logoUrl" 
                alt="Logo" 
                class="h-8 w-auto object-contain"
              />
              <svg v-else class="h-8 w-8 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              <span class="ml-2 text-xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">OrderValut</span>
            </router-link>
          </div>

          <!-- Right side nav items -->
          <div class="flex items-center gap-3">
            <!-- Dark Mode Toggle -->
            <button
              @click="toggleDarkMode"
              class="p-2 rounded-lg transition-all duration-200"
              :class="[
                isDarkMode 
                  ? 'text-yellow-400 hover:bg-gray-700 hover:text-yellow-300' 
                  : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
              ]"
              :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
            >
              <svg v-if="!isDarkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
              </svg>
            </button>

            <!-- Subscription Status Indicator -->
            <div v-if="isSubscriptionValid" 
                 class="hidden sm:flex items-center gap-1 text-xs px-2 py-1 rounded-full"
                 :class="isDarkMode ? 'text-green-400 bg-green-900/30' : 'text-green-600 bg-green-50'">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              {{ planName || 'Active' }}
            </div>
            <div v-else-if="!isSubscriptionPage" 
                 class="hidden sm:flex items-center gap-1 text-xs px-2 py-1 rounded-full"
                 :class="isDarkMode ? 'text-red-400 bg-red-900/30' : 'text-red-600 bg-red-50'">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              Inactive
            </div>

            <!-- User Dropdown -->
            <div class="relative" @click.stop="toggleDropdown">
              <button class="flex items-center text-sm rounded-full focus:ring-4" 
                      :class="[isDarkMode ? 'bg-gray-700 focus:ring-gray-600' : 'bg-gray-100 focus:ring-gray-200']">
                <span class="sr-only">Open user menu</span>
                <div class="w-8 h-8 rounded-full bg-blue-600 dark:bg-blue-500 flex items-center justify-center text-white font-semibold">
                  {{ getInitials(vendorName) }}
                </div>
              </button>
              
              <!-- Dropdown menu -->
              <div v-if="isDropdownOpen" 
                   class="absolute right-0 mt-2 w-48 rounded-lg shadow-2xl py-1 z-50 border"
                   :class="[isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
                <div class="px-4 py-3 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
                  <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ vendorName }}</p>
                  <p class="text-xs truncate" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ authStore.user?.value?.email || 'vendor@example.com' }}</p>
                </div>
                <router-link to="/vendor/profile" 
                             class="flex items-center px-4 py-2 text-sm transition-colors"
                             :class="[isDarkMode ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-100']">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Profile
                </router-link>
                <router-link to="/vendor/settings" 
                             class="flex items-center px-4 py-2 text-sm transition-colors"
                             :class="[isDarkMode ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-100']">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  Settings
                </router-link>
                <router-link to="/vendor/subscription" 
                             class="flex items-center px-4 py-2 text-sm transition-colors"
                             :class="[isDarkMode ? 'text-gray-300 hover:bg-gray-700 hover:text-white' : 'text-gray-700 hover:bg-gray-100']">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Subscription
                </router-link>
                <div class="border-t" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'"></div>
                <button @click="logout" 
                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 transition-colors"
                        :class="isDarkMode ? 'hover:bg-red-900/20' : 'hover:bg-red-50'">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Sidebar -->
    <aside 
      :class="[
        'fixed left-0 z-40 w-64 h-[calc(100vh-64px)] transition-transform duration-300 border-r',
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'lg:translate-x-0',
        isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
      ]"
      :style="{ top: '64px' }"
    >
      <div class="h-full px-3 pb-4 overflow-y-auto">
        <ul class="space-y-2 font-medium">
          <!-- Dashboard -->
          <li>
            <router-link 
              to="/vendor/dashboard" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path === '/vendor/dashboard' 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" 
                   :class="[$route.path === '/vendor/dashboard' ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" 
                   fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
              <span class="ml-3">Dashboard</span>
            </router-link>
          </li>

          <!-- Orders -->
          <li>
            <router-link 
              to="/vendor/orders" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path.startsWith('/vendor/orders') 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" 
                   :class="[$route.path.startsWith('/vendor/orders') ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" 
                   fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3.5-7A1 1 0 0017.5 2H6.55l-.31-1.243A1 1 0 005.22 1H3zm3.5 4a.5.5 0 000 1h10a.5.5 0 000-1h-10zM6 17a2 2 0 100 4 2 2 0 000-4zm9 0a2 2 0 100 4 2 2 0 000-4z"/>
              </svg>
              <span class="ml-3">Orders</span>
            </router-link>
          </li>

          <!-- categories -->
          <li>
            <router-link 
              to="/vendor/categories" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path.startsWith('/vendor/categories') 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" 
                   :class="[$route.path.startsWith('/vendor/categories') ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" 
                   fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6zm1 2a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z"/>
              </svg>
              <span class="ml-3">Categories</span>
            </router-link>
          </li>
          <!-- Products -->
          <li>
            <router-link 
              to="/vendor/products" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path.startsWith('/vendor/products') 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" 
                   :class="[$route.path.startsWith('/vendor/products') ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" 
                   fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6zm1 2a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm0 4a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z"/>
              </svg>
              <span class="ml-3">Products</span>
            </router-link>
          </li>

          <!-- Customers -->
          <li>
            <router-link 
              to="/vendor/customers" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path.startsWith('/vendor/customers') 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" 
                   :class="[$route.path.startsWith('/vendor/customers') ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" 
                   fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
              </svg>
              <span class="ml-3">Customers</span>
            </router-link>
          </li>

          <!-- Analytics -->
          <li>
            <router-link 
              to="/vendor/analytics" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path.startsWith('/vendor/analytics') 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" 
                   :class="[$route.path.startsWith('/vendor/analytics') ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" 
                   fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
              </svg>
              <span class="ml-3">Analytics</span>
            </router-link>
          </li>

          <!-- Settings -->
          <li class="border-t pt-2 mt-2" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
            <router-link 
              to="/vendor/settings" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path.startsWith('/vendor/settings') 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" 
                   :class="[$route.path.startsWith('/vendor/settings') ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" 
                   fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-3">Settings</span>
            </router-link>
          </li>
        
          <!-- Subscription -->
          <li>
            <router-link 
              to="/vendor/subscription" 
              :class="[
                'flex items-center p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 group',
                $route.path.startsWith('/vendor/subscription') 
                  ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                  : isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-700 hover:text-gray-900'
              ]"
              @click="closeSidebarOnMobile"
            >
              <svg class="w-5 h-5 transition duration-75 group-hover:text-blue-600 dark:group-hover:text-blue-400" 
                   :class="[$route.path.startsWith('/vendor/subscription') ? 'text-blue-600 dark:text-blue-400' : (isDarkMode ? 'text-gray-400' : 'text-gray-500')]" 
                   fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 4H4zm4 4a1 1 0 000 2h4a1 1 0 100-2H8zm-1 4a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
              <span class="ml-3">Subscription</span>
              <span v-if="!isSubscriptionValid" 
                    class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full animate-pulse">
                Expired
              </span>
              <span v-else-if="isTrialExpiringSoon" 
                    class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">
                {{ trialDaysLeft }}d
              </span>
              <span v-else-if="isSubscriptionValid" 
                    class="ml-auto bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">
                Active
              </span>
            </router-link>
          </li>
        </ul>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="p-4 lg:ml-64 mt-16 transition-all duration-300" 
         :class="{ 'mt-24': !isSubscriptionValid && !isSubscriptionPage }">
      <div class="p-4 rounded-lg">
        <router-view />
      </div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div 
      v-if="isSidebarOpen" 
      @click="toggleSidebar"
      class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 lg:hidden"
    ></div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useAuthStore } from '../store';
import { useRoute } from 'vue-router';
import axios from 'axios';

export default {
  setup() {
    const authStore = useAuthStore();
    const route = useRoute();
    const isSidebarOpen = ref(false);
    const isDropdownOpen = ref(false);
    const isSubscriptionValid = ref(true);
    const subscriptionMessage = ref('');
    const planName = ref('');
    const trialDaysLeft = ref(0);
    const isTrialExpiringSoon = ref(false);
    const isDarkMode = ref(false);
    
    // Logo ref
    const logoUrl = ref(null);

    // Function to update logo from localStorage
    const updateLogoFromStorage = () => {
      const storedLogo = localStorage.getItem('logo');
      if (storedLogo) {
        try {
          const logoData = JSON.parse(storedLogo);
          logoUrl.value = logoData.url || null;
        } catch {
          logoUrl.value = storedLogo;
        }
      } else {
        logoUrl.value = null;
      }
    };

    // Initial logo load
    updateLogoFromStorage();

    const isSubscriptionPage = computed(() => {
      return route.path === '/vendor/subscription' || route.path.startsWith('/vendor/subscription/');
    });

    const vendorName = computed(() => {
      const user = authStore.user?.value;
      return user?.business_name || user?.name || 'Vendor';
    });

    const getInitials = (name) => {
      if (!name) return 'V';
      return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
    };

    const toggleSidebar = () => {
      isSidebarOpen.value = !isSidebarOpen.value;
    };

    const closeSidebarOnMobile = () => {
      if (window.innerWidth < 1024) {
        isSidebarOpen.value = false;
      }
    };

    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value;
    };

    const loadThemePreference = () => {
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme === 'dark') {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
      } else if (savedTheme === 'light') {
        isDarkMode.value = false;
        document.documentElement.classList.remove('dark');
      } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
      } else {
        isDarkMode.value = false;
        document.documentElement.classList.remove('dark');
      }
    };

    const toggleDarkMode = () => {
      isDarkMode.value = !isDarkMode.value;
      if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
      } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
      }
    };

    const logout = async () => {
      await authStore.logout();
      window.location.href = '/login';
    };

    // Check subscription status
    const checkSubscriptionStatus = async () => {
      try {
        const response = await axios.get('/api/vendor/subscription/status');
        if (response.data.success) {
          const data = response.data.data;
          isSubscriptionValid.value = data.is_valid;
          subscriptionMessage.value = data.message;
          planName.value = data.plan_name;
          
          // Check trial status
          if (data.trial_ends_at) {
            const trialEnd = new Date(data.trial_ends_at);
            const now = new Date();
            const daysLeft = Math.ceil((trialEnd - now) / (1000 * 60 * 60 * 24));
            trialDaysLeft.value = daysLeft > 0 ? daysLeft : 0;
            isTrialExpiringSoon.value = daysLeft > 0 && daysLeft <= 7;
          }
          
          // If subscription is invalid and not on subscription page, redirect
          if (!data.is_valid && !isSubscriptionPage.value) {
            // Redirect to subscription page
            window.location.href = '/vendor/subscription';
          }
        }
      } catch (error) {
        console.error('Error checking subscription status:', error);
      }
    };

    // Close dropdown when clicking outside
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        isDropdownOpen.value = false;
      }
    };

    // Listen for logo updates
    const handleLogoUpdate = (event) => {
      updateLogoFromStorage();
    };

    // Listen for storage changes (for cross-tab)
    const handleStorageChange = (event) => {
      if (event.key === 'logo') {
        updateLogoFromStorage();
      }
    };

    onMounted(() => {
      document.addEventListener('click', handleClickOutside);
      loadThemePreference();
      checkSubscriptionStatus();
      
      // Check subscription status every minute
      const interval = setInterval(checkSubscriptionStatus, 60000);

      // Listen for custom logo-updated event
      window.addEventListener('logo-updated', handleLogoUpdate);
      
      // Listen for storage events (cross-tab)
      window.addEventListener('storage', handleStorageChange);

      const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
      const handleSystemThemeChange = (e) => {
        if (!localStorage.getItem('theme')) {
          isDarkMode.value = e.matches;
          if (e.matches) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
        }
      };
      darkModeMediaQuery.addEventListener('change', handleSystemThemeChange);
      
      // Cleanup
      return () => {
        clearInterval(interval);
        document.removeEventListener('click', handleClickOutside);
        window.removeEventListener('logo-updated', handleLogoUpdate);
        window.removeEventListener('storage', handleStorageChange);
        darkModeMediaQuery.removeEventListener('change', handleSystemThemeChange);
      };
    });

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
    });

    return {
      authStore,
      vendorName,
      logoUrl,
      isSidebarOpen,
      isDropdownOpen,
      isSubscriptionValid,
      subscriptionMessage,
      planName,
      trialDaysLeft,
      isTrialExpiringSoon,
      isSubscriptionPage,
      isDarkMode,
      getInitials,
      toggleSidebar,
      closeSidebarOnMobile,
      toggleDropdown,
      toggleDarkMode,
      logout,
      checkSubscriptionStatus
    };
  }
};
</script>

<style scoped>
.router-link-active {
  background-color: #eff6ff;
  color: #2563eb;
}

.dark .router-link-active {
  background-color: rgba(37, 99, 235, 0.2);
  color: #60a5fa;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

.transition-transform {
  transition: transform 0.3s ease-in-out;
}

.relative .absolute {
  animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>