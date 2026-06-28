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
      
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ vendor?.business_name || 'Vendor Details' }}</span>
    </nav>

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
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ error }}
      </div>
    </div>

    <!-- Content -->
    <div v-else>
      <!-- Vendor Info Card -->
      <div :class="[
        'rounded-2xl shadow-lg p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
          <!-- Back Button -->
          <router-link to="/admin/vendors" class="p-2 rounded-lg transition-colors flex-shrink-0" :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'" aria-label="Back to vendors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
          </router-link>

          <!-- Avatar -->
          <div class="flex-shrink-0">
              <div v-if="vendor.business_logo" class="w-24 h-24 rounded-xl border-2 border-gray-200 overflow-hidden bg-gray-50">
                                        <img :src="'/storage/' + vendor.business_logo" alt="Business Logo" class="w-full h-full object-cover">
                                    </div>
            <div v-else class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
              {{ getInitials(vendor.business_name) }}
            </div>
          </div>
          
          <!-- Info -->
          <div class="flex-1">
            <div class="flex flex-wrap items-start justify-between gap-2">
              <div>
                <h1 class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">{{ vendor.business_name }}</h1>
                <p class="flex items-center" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
                  <svg class="w-4 h-4 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  {{ vendor.user?.name }}
                </p>
              </div>
              <div class="flex flex-wrap gap-2">
                <button @click="editVendor" class="inline-flex items-center px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md text-sm font-medium">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  Edit
                </button>
                <button @click="deleteVendor" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium shadow-md">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Delete
                </button>
              </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 mt-3">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" :class="vendor.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'">
                <svg class="w-2 h-2 mr-1.5" :class="vendor.is_active ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                  <circle cx="10" cy="10" r="10"/>
                </svg>
                {{ vendor.is_active ? 'Active' : 'Inactive' }}
              </span>
              <span class="text-sm flex items-center" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
                <svg class="w-4 h-4 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ vendor.user?.email }}
              </span>
              <span class="text-sm flex items-center" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
                <svg class="w-4 h-4 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ formatDate(vendor.created_at) }}
              </span>
              <span class="text-sm flex items-center" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
                <svg class="w-4 h-4 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                {{ statistics.total_products || 0 }} Products
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
        <nav class="flex flex-wrap -mb-px gap-1">
          <button 
            @click="activeTab = 'orders'" 
            class="px-5 py-2.5 text-sm font-medium border-b-2 transition-all rounded-t-lg"
            :class="activeTab === 'orders' ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Orders <span class="ml-1 px-2 py-0.5 rounded-full text-xs" :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'">{{ orders.length }}</span>
          </button>
          <button 
            @click="activeTab = 'products'" 
            class="px-5 py-2.5 text-sm font-medium border-b-2 transition-all rounded-t-lg"
            :class="activeTab === 'products' ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Products <span class="ml-1 px-2 py-0.5 rounded-full text-xs" :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-200 text-gray-700'">{{ products.length }}</span>
          </button>
          <button 
            @click="activeTab = 'statistics'" 
            class="px-5 py-2.5 text-sm font-medium border-b-2 transition-all rounded-t-lg"
            :class="activeTab === 'statistics' ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Statistics
          </button>
        </nav>
      </div>

      <!-- Orders Tab -->
      <div v-if="activeTab === 'orders'" :class="[
        'rounded-2xl shadow-lg overflow-hidden',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="p-4 border-b flex flex-wrap items-center justify-between gap-2" :class="isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'">
          <h2 class="text-lg font-semibold flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Orders
          </h2>
          <div class="flex flex-wrap gap-2">
            <div class="relative">
              <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input 
                type="text" 
                v-model="orderSearch" 
                placeholder="Search orders..." 
                :class="[
                  'pl-9 pr-3 py-1.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-48 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
            </div>
            <select v-model="orderStatus" :class="[
              'border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
            ]">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        
        <div v-if="filteredOrders.length === 0" class="text-center py-16" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
          <svg class="w-16 h-16 mx-auto mb-3" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
          </svg>
          No orders found
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Order #</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Customer</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
              <tr v-for="order in filteredOrders" :key="order.id" class="transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
                <td class="px-4 py-3 text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">#{{ order.order_number }}</td>
                <td class="px-4 py-3 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ formatDate(order.created_at) }}</td>
                <td class="px-4 py-3 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ order.customer?.name || 'N/A' }}</td>
                <td class="px-4 py-3 text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatCurrency(order.total_amount) }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" :class="getStatusBadge(order.status)">
                    <svg class="w-2 h-2 mr-1.5 mt-0.5" :class="getStatusDot(order.status)" fill="currentColor" viewBox="0 0 20 20">
                      <circle cx="10" cy="10" r="10"/>
                    </svg>
                    {{ order.status }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <button @click="viewOrder(order.id)" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                    View <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Products Tab -->
      <div v-if="activeTab === 'products'" :class="[
        'rounded-2xl shadow-lg overflow-hidden',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="p-4 border-b flex flex-wrap items-center justify-between gap-2" :class="isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'">
          <h2 class="text-lg font-semibold flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Products
          </h2>
          <div class="flex flex-wrap gap-2">
            <div class="relative">
              <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input 
                type="text" 
                v-model="productSearch" 
                placeholder="Search products..." 
                :class="[
                  'pl-9 pr-3 py-1.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-48 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
            </div>
            <button @click="addProduct" class="inline-flex items-center px-4 py-1.5 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md text-sm font-medium">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Add Product
            </button>
          </div>
        </div>
        
        <div v-if="filteredProducts.length === 0" class="text-center py-16" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
          <svg class="w-16 h-16 mx-auto mb-3" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          No products found
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Product</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">SKU</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Price</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Stock</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
              <tr v-for="product in filteredProducts" :key="product.id" class="transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
                <td class="px-4 py-3">
                  <div class="flex items-center">
                    <img v-if="product.image" :src="product.image" :alt="product.name" class="w-10 h-10 object-cover rounded-lg mr-3 border" :class="isDarkMode ? 'border-gray-600' : 'border-gray-200'">
                    <div v-else class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-100'">
                      <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                      </svg>
                    </div>
                    <div>
                      <div class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ product.name }}</div>
                      <div class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ product.description }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'"><code class="px-2 py-1 rounded text-xs" :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'">{{ product.sku }}</code></td>
                <td class="px-4 py-3 text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatCurrency(product.price) }}</td>
                <td class="px-4 py-3 text-sm font-medium" :class="getStockBadge(product.stock)">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                  {{ product.stock }}
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" :class="product.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'">
                    <svg class="w-2 h-2 mr-1.5" :class="product.is_active ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                      <circle cx="10" cy="10" r="10"/>
                    </svg>
                    {{ product.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex gap-2">
                    <button @click="editProduct(product.id)" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                      <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Edit
                    </button>
                    <button @click="deleteProduct(product.id)" class="text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                      <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Statistics Tab -->
      <div v-if="activeTab === 'statistics'" class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div :class="[
            'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <div class="flex items-center">
              <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Orders</p>
                <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">{{ statistics.total_orders || 0 }}</p>
              </div>
            </div>
          </div>

          <div :class="[
            'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <div class="flex items-center">
              <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Revenue</p>
                <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">{{ formatCurrency(statistics.total_revenue || 0) }}</p>
              </div>
            </div>
          </div>

          <div :class="[
            'rounded-2xl shadow-lg p-5 border-l-4 border-purple-500',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <div class="flex items-center">
              <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Products</p>
                <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">{{ statistics.total_products || 0 }}</p>
              </div>
            </div>
          </div>

         
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div :class="[
            'rounded-2xl shadow-lg p-5',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3m0 0l3 3m-3-3v12M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Revenue Trend
            </h3>
            <div class="h-72">
              <canvas id="revenueChart"></canvas>
            </div>
          </div>

          <div :class="[
            'rounded-2xl shadow-lg p-5',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
              <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
              </svg>
              Order Status Distribution
            </h3>
            <div class="h-72">
              <canvas id="orderStatusChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const route = useRoute();
const router = useRouter();

// Data
const loading = ref(true);
const error = ref(null);
const vendor = ref({});
const orders = ref([]);
const products = ref([]);
const statistics = ref({});
const activeTab = ref('orders');
const orderSearch = ref('');
const orderStatus = ref('');
const productSearch = ref('');
const isDarkMode = ref(false);
let darkModeObserver = null;

// Chart instances
const revenueChartInstance = ref(null);
const orderStatusChartInstance = ref(null);

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
          // Re-init charts when theme changes
          if (activeTab.value === 'statistics') {
            setTimeout(() => initCharts(), 200);
          }
        }
      }
    });
  });
  darkModeObserver.observe(htmlElement, { attributes: true });
};

// Computed
const filteredOrders = computed(() => {
  let filtered = orders.value;
  
  if (orderSearch.value) {
    const search = orderSearch.value.toLowerCase();
    filtered = filtered.filter(order => 
      order.order_number?.toLowerCase().includes(search) ||
      order.customer?.name?.toLowerCase().includes(search)
    );
  }
  
  if (orderStatus.value) {
    filtered = filtered.filter(order => order.status === orderStatus.value);
  }
  
  return filtered;
});

const filteredProducts = computed(() => {
  if (!productSearch.value) return products.value;
  
  const search = productSearch.value.toLowerCase();
  return products.value.filter(product =>
    product.name?.toLowerCase().includes(search) ||
    product.sku?.toLowerCase().includes(search) ||
    product.category?.name?.toLowerCase().includes(search)
  );
});

// Methods
const fetchVendor = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await axios.get(`/api/admin/vendors/${route.params.id}`);
    const data = response.data;
    
    vendor.value = data.vendor || {};
    orders.value = data.orders || [];
    products.value = data.products || [];
    statistics.value = data.statistics || {};
    
    if (activeTab.value === 'statistics') {
      await nextTick();
      initCharts();
    }
  } catch (err) {
    console.error('Error fetching vendor:', err);
    error.value = err.response?.data?.message || 'Failed to load vendor data';
  } finally {
    loading.value = false;
  }
};

const initCharts = () => {
  // Destroy existing charts
  if (revenueChartInstance.value) {
    revenueChartInstance.value.destroy();
    revenueChartInstance.value = null;
  }
  if (orderStatusChartInstance.value) {
    orderStatusChartInstance.value.destroy();
    orderStatusChartInstance.value = null;
  }

  // Small delay to ensure DOM is ready
  setTimeout(() => {
    initRevenueChart();
    initOrderStatusChart();
  }, 100);
};

const getChartColors = () => {
  return {
    revenue: isDarkMode.value ? '#60A5FA' : '#3B82F6',
    revenueBg: isDarkMode.value ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)',
    gridColor: isDarkMode.value ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
    textColor: isDarkMode.value ? '#9CA3AF' : '#6B7280',
  };
};

const initRevenueChart = () => {
  const canvas = document.getElementById('revenueChart');
  if (!canvas) return;
  
  const ctx = canvas.getContext('2d');
  if (!ctx) return;
  
  const colors = getChartColors();
  const revenueData = statistics.value.revenue_by_date || [];
  const labels = revenueData.map(d => d.date);
  const data = revenueData.map(d => d.total);
  
  revenueChartInstance.value = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels.length ? labels : ['No Data'],
      datasets: [{
        label: 'Revenue',
        data: data.length ? data : [0],
        borderColor: colors.revenue,
        backgroundColor: colors.revenueBg,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: colors.revenue,
        pointBorderColor: isDarkMode.value ? '#1F2937' : '#fff',
        pointBorderWidth: 2,
        pointRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: colors.gridColor
          },
          ticks: {
            color: colors.textColor,
            callback: value => formatCurrency(value)
          }
        },
        x: {
          grid: {
            color: colors.gridColor
          },
          ticks: {
            color: colors.textColor
          }
        }
      }
    }
  });
};

const initOrderStatusChart = () => {
  const canvas = document.getElementById('orderStatusChart');
  if (!canvas) return;
  
  const ctx = canvas.getContext('2d');
  if (!ctx) return;
  
  const statusData = statistics.value.status_distribution || {};
  const labels = Object.keys(statusData);
  const data = Object.values(statusData);
  const colors = {
    pending: '#f59e0b',
    processing: '#3b82f6',
    completed: '#10b981',
    cancelled: '#ef4444',
    refunded: '#6b7280'
  };
  
  const chartData = labels.length ? {
    labels: labels,
    datasets: [{
      data: data,
      backgroundColor: labels.map(label => colors[label] || '#6b7280'),
      borderWidth: 2,
      borderColor: isDarkMode.value ? '#1F2937' : '#fff'
    }]
  } : {
    labels: ['No Data'],
    datasets: [{
      data: [1],
      backgroundColor: ['#e5e7eb']
    }]
  };
  
  orderStatusChartInstance.value = new Chart(ctx, {
    type: 'doughnut',
    data: chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 15,
            usePointStyle: true,
            pointStyle: 'circle',
            color: isDarkMode.value ? '#9CA3AF' : '#6B7280',
            font: {
              size: 12
            }
          }
        }
      },
      cutout: '65%'
    }
  });
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatCurrency = (amount) => {
  if (!amount) return '$0.00';
  return `$${parseFloat(amount).toFixed(2)}`;
};

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
};

const getStatusBadge = (status) => {
  const badges = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  };
  return badges[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getStatusDot = (status) => {
  const dots = {
    pending: 'text-yellow-500 dark:text-yellow-400',
    processing: 'text-blue-500 dark:text-blue-400',
    completed: 'text-green-500 dark:text-green-400',
    cancelled: 'text-red-500 dark:text-red-400',
    refunded: 'text-gray-500 dark:text-gray-400'
  };
  return dots[status] || 'text-gray-500 dark:text-gray-400';
};

const getStockBadge = (stock) => {
  if (!stock || stock <= 0) return 'text-red-600 dark:text-red-400';
  if (stock <= 10) return 'text-yellow-600 dark:text-yellow-400';
  return 'text-green-600 dark:text-green-400';
};

const goBack = () => {
  router.push('/admin/vendors');
};

const editVendor = () => {
  router.push(`/admin/vendors/${route.params.id}/edit`);
};

const deleteVendor = () => {
  if (confirm('Are you sure you want to delete this vendor?')) {
    console.log('Delete vendor:', route.params.id);
  }
};

const viewOrder = (orderId) => {
  router.push(`/admin/orders/${orderId}`);
};

const addProduct = () => {
  router.push(`/admin/vendors/${route.params.id}/products/create`);
};

const editProduct = (productId) => {
  router.push(`/admin/vendors/${route.params.id}/products/${productId}/edit`);
};

const deleteProduct = (productId) => {
  if (confirm('Are you sure you want to delete this product?')) {
    console.log('Delete product:', productId);
  }
};

// Watch for tab changes
watch(activeTab, (newTab) => {
  if (newTab === 'statistics') {
    nextTick(() => {
      initCharts();
    });
  }
});

watch(() => route.params.id, () => {
  fetchVendor();
});

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
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
  if (revenueChartInstance.value) {
    revenueChartInstance.value.destroy();
    revenueChartInstance.value = null;
  }
  if (orderStatusChartInstance.value) {
    orderStatusChartInstance.value.destroy();
    orderStatusChartInstance.value = null;
  }
});
</script>

<style scoped>
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
</style>