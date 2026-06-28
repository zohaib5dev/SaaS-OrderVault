<template>
  <div class="subscriptions-page" :class="{ 'dark-mode': isDarkMode }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div>
        <nav class="flex items-center gap-2 text-sm mb-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
          <router-link to="/vendor/dashboard" :class="isDarkMode ? 'hover:text-gray-200' : 'hover:text-blue-600'" class="transition-colors">
            Dashboard
          </router-link>
          <span :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'">/</span>
          <span :class="isDarkMode ? 'text-gray-200' : 'text-gray-700'" class="font-medium">Subscriptions</span>
        </nav>
        <h1 :class="isDarkMode ? 'text-white' : 'text-gray-800'" class="text-2xl font-bold">My Subscriptions</h1>
        <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm mt-1">Manage your plan subscriptions and view invoices</p>
      </div>
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
      
        <!-- Status badge when account is active -->
        <span v-if="stripeAccountStatus === 'active'" class="px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium flex items-center gap-2">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          Stripe Connected
        </span>
        <span v-if="stripeAccountStatus === 'pending'" class="px-3 py-1.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-lg text-sm font-medium flex items-center gap-2">
          <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Pending Setup
        </span>
        <button 
          @click="refreshData" 
          :class="[
            'px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2 text-sm',
            isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <div v-else>
      <!-- Stripe Onboarding Banner for vendors without Stripe -->
      <div v-if="stripeAccountStatus === 'not_created' || stripeAccountStatus === 'pending'" 
           :class="[
             'rounded-xl shadow-sm border p-6 mb-8',
             isDarkMode ? 'bg-gray-800 border-yellow-700' : 'bg-yellow-50 border-yellow-200'
           ]">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div class="flex items-start gap-4">
            <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
              <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <div>
              <h4 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="font-semibold">
                {{ stripeAccountStatus === 'not_created' ? 'Connect Your Stripe Account' : 'Complete Stripe Setup' }}
              </h4>
              <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" class="text-sm">
                {{ stripeAccountStatus === 'not_created' 
                  ? 'Set up Stripe to accept payments and manage subscriptions.' 
                  : 'Please complete your Stripe account setup to start accepting payments.' }}
              </p>
            </div>
          </div>
          <button 
            @click="startOnboarding"
            :disabled="onboarding"
            class="px-6 py-2.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm font-medium whitespace-nowrap flex items-center gap-2"
          >
            <svg v-if="!onboarding" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ stripeAccountStatus === 'not_created' ? 'Connect Stripe' : 'Continue Setup' }}
          </button>
        </div>
      </div>

      <!-- Current Subscription / Plan Status -->
      <div class="mb-8">
        <!-- Active Subscription -->
        <div v-if="currentSubscription" :class="[
          'rounded-xl shadow-sm border overflow-hidden',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
        ]">
          <!-- Header with gradient -->
          <div :class="[
            'px-6 py-4 border-b',
            isDarkMode ? 'bg-gray-700/50 border-gray-700' : 'bg-gradient-to-r from-blue-50 to-indigo-50 border-gray-100'
          ]">
            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Current Plan</span>
            </div>
          </div>
          
          <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
              <div class="flex items-start gap-4">
                <div class="p-3 rounded-xl" :class="currentSubscription.type === 'subscription' ? 'bg-purple-100 dark:bg-purple-900/30' : 'bg-green-100 dark:bg-green-900/30'">
                  <svg class="w-6 h-6" :class="currentSubscription.type === 'subscription' ? 'text-purple-600 dark:text-purple-400' : 'text-green-600 dark:text-green-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <h2 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-xl font-bold">{{ currentSubscription.plan_name }}</h2>
                  <div class="flex flex-wrap items-center gap-2 mt-1">
                    <span class="text-xs px-2 py-1 rounded-full" :class="currentSubscription.type === 'subscription' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'">
                      {{ currentSubscription.type === 'subscription' ? 'Subscription' : 'One-time' }}
                    </span>
                    <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-500'">•</span>
                    <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-semibold">${{ formatNumber(currentSubscription.amount) }}</span>
                    <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm" v-if="currentSubscription.type === 'subscription'">/ {{ currentSubscription.billing_cycle || 'month' }}</span>
                  </div>
                </div>
              </div>
              <div class="flex flex-col items-start md:items-end w-full md:w-auto">
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs font-medium">Status</span>
                <span class="text-sm px-3 py-1 rounded-full font-medium" :class="getSubscriptionStatusClass(currentSubscription.status)">
                  {{ formatStatus(currentSubscription.status) }}
                </span>
                <span v-if="currentSubscription.status === 'active' && currentSubscription.type === 'subscription'" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs mt-1">
                  Next billing: {{ formatDate(currentSubscription.next_billing_date) }}
                </span>
              </div>
            </div>

            <!-- Subscription Details -->
            <div class="mt-6 pt-6 border-t grid grid-cols-2 sm:grid-cols-4 gap-4" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <div>
                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider">Started</p>
                <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">{{ formatDate(currentSubscription.started_at) }}</p>
              </div>
              <div v-if="currentSubscription.type === 'subscription'">
                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider">Billing Cycle</p>
                <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-medium capitalize">{{ currentSubscription.billing_cycle || 'Monthly' }}</p>
              </div>
              <div v-if="currentSubscription.type === 'subscription'">
                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider">Next Payment</p>
                <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">{{ formatDate(currentSubscription.next_billing_date) }}</p>
              </div>
              <div>
                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs uppercase tracking-wider">Total Paid</p>
                <p :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-medium">${{ formatNumber(currentSubscription.total_paid || currentSubscription.amount) }}</p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 pt-6 border-t flex flex-wrap gap-3" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <button 
                v-if="currentSubscription.status === 'active' && currentSubscription.type === 'subscription'" 
                @click="cancelSubscription" 
                class="px-4 py-2 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-sm flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel Subscription
              </button>
            </div>
          </div>
        </div>

        <!-- Trial Mode -->
        <div v-else-if="trialMode" :class="[
          'rounded-xl shadow-sm border overflow-hidden',
          isDarkMode ? 'bg-gray-800 border-yellow-700' : 'bg-gradient-to-r from-yellow-50 to-orange-50 border-yellow-200'
        ]">
          <div :class="[
            'px-6 py-4 border-b',
            isDarkMode ? 'bg-gray-700/50 border-yellow-700' : 'bg-yellow-100/50 border-yellow-200'
          ]">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span class="text-xs font-semibold text-yellow-700 dark:text-yellow-300 uppercase tracking-wider">Free Trial</span>
            </div>
          </div>
          <div class="p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
              <div class="flex-1">
                <h3 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-bold">You're on a Free Trial</h3>
                <p :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" class="text-sm mt-1">
                  Your trial ends in 
                  <span class="font-bold text-yellow-700 dark:text-yellow-400">{{ trialDaysLeft }} days</span>.
                </p>
                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs mt-1">
                  Subscribe to a plan to continue using all features after the trial ends.
                </p>
              </div>
              <button @click="scrollToPlans" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm font-medium whitespace-nowrap">
                View Plans
              </button>
            </div>
          </div>
        </div>

        <!-- No Subscription -->
        <div v-else :class="[
          'rounded-xl shadow-sm border p-8 text-center',
          isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
        ]">
          <div class="max-w-md mx-auto">
            <div :class="[
              'w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4',
              isDarkMode ? 'bg-gray-700' : 'bg-gray-100'
            ]">
              <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-medium mb-2">No Active Subscription</h3>
            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="mb-6">Choose a plan below to get started with premium features.</p>
            <button @click="scrollToPlans" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              Browse Plans
            </button>
          </div>
        </div>
      </div>

      <!-- Available Plans -->
      <div v-if="currentSubscription == null || currentSubscription.type == 'subscription' " id="plans-section" class="mb-8">
        <div class="flex items-center gap-2 mb-4">
          <h3 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold">Available Plans</h3>
          <span :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-500'" class="text-xs px-2 py-1 rounded-full">{{ plans.length }} plans</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="plan in plans" 
            :key="plan.id" 
            :class="[
              'rounded-xl shadow-sm border overflow-hidden transition-all duration-300 group',
              isDarkMode ? 'bg-gray-800 border-gray-700 hover:border-gray-500' : 'bg-white border-gray-100 hover:shadow-md hover:border-blue-200'
            ]"
          >
            <div class="p-6">
              <div class="flex items-center justify-between mb-2">
                <h4 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-bold">{{ plan.name }}</h4>
                <span class="text-xs px-2 py-1 rounded-full" :class="plan.type === 'subscription' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'">
                  {{ plan.type === 'subscription' ? 'Subscription' : 'One-time' }}
                </span>
              </div>
              <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm mb-4 line-clamp-2">{{ plan.description || 'No description available' }}</p>
              <div class="flex items-baseline gap-1 mb-4">
                <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-3xl font-bold">${{ formatNumber(plan.price) }}</span>
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm" v-if="plan.type === 'subscription'">/ {{ plan.billing_cycle || 'month' }}</span>
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm" v-else>one-time</span>
              </div>
              <ul class="space-y-2 mb-6">
                <li v-for="feature in plan.features" :key="feature" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" class="flex items-start gap-2 text-sm">
                  <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  {{ feature }}
                </li>
              </ul>
              <button 
                @click="selectPlan(plan)" 
                :disabled="isPlanActive(plan.id)"
                class="w-full py-2.5 px-4 rounded-lg transition-colors text-sm font-medium"
                :class="isPlanActive(plan.id) 
                  ? (isDarkMode ? 'bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-gray-100 text-gray-500 cursor-not-allowed') 
                  : 'bg-blue-600 text-white hover:bg-blue-700 group-hover:shadow-md'"
              >
                {{ isPlanActive(plan.id) ? '✓ Current Plan' : 'Select Plan' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Invoices Section -->
      <div :class="[
        'rounded-xl shadow-sm border overflow-hidden',
        isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'
      ]">
        <div :class="[
          'px-6 py-4 border-b flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2',
          isDarkMode ? 'border-gray-700 bg-gray-700/50' : 'border-gray-100 bg-gray-50/50'
        ]">
          <div>
            <h3 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-semibold flex items-center gap-2">
              <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Invoice History
            </h3>
            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm mt-0.5">All your plan purchase invoices</p>
          </div>
          <span :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-500'" class="text-sm px-3 py-1 rounded-full">{{ allInvoices.length }} invoices</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[600px]">
            <thead>
              <tr :class="[
                'text-left text-xs uppercase border-b',
                isDarkMode ? 'text-gray-400 border-gray-700 bg-gray-700/30' : 'text-gray-500 border-gray-100 bg-gray-50'
              ]">
                <th class="px-6 py-3 font-semibold">Invoice #</th>
                <th class="px-6 py-3 font-semibold">Plan</th>
                <th class="px-6 py-3 font-semibold">Type</th>
                <th class="px-6 py-3 text-right font-semibold">Amount</th>
                <th class="px-6 py-3 font-semibold">Status</th>
                <th class="px-6 py-3 font-semibold hidden sm:table-cell">Date</th>
                <th class="px-6 py-3 text-right font-semibold">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="invoice in invoices" 
                :key="invoice.id" 
                :class="[
                  'border-b transition-colors',
                  isDarkMode ? 'border-gray-700 hover:bg-gray-700/30' : 'border-gray-100 hover:bg-gray-50'
                ]"
              >
                <td class="px-6 py-3">
                  <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ invoice.invoice_number || '#' + invoice.id }}</span>
                </td>
                <td class="px-6 py-3">
                  <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm">{{ invoice.plan_name }}</span>
                </td>
                <td class="px-6 py-3">
                  <span class="text-xs px-2 py-1 rounded-full" :class="invoice.type === 'subscription' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'">
                    {{ invoice.type === 'subscription' ? 'Subscription' : 'One-time' }}
                  </span>
                </td>
                <td class="px-6 py-3 text-right">
                  <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-sm font-semibold">${{ formatNumber(invoice.amount) }}</span>
                </td>
                <td class="px-6 py-3">
                  <span class="text-xs px-2 py-1 rounded-full" :class="getInvoiceStatusClass(invoice.status)">
                    {{ formatStatus(invoice.status) }}
                  </span>
                </td>
                <td class="px-6 py-3 hidden sm:table-cell">
                  <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm">{{ formatDate(invoice.created_at) }}</span>
                </td>
                <td class="px-6 py-3 text-right">
                  <button @click="downloadInvoice(invoice.id)" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium flex items-center gap-1 ml-auto transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg> 
                  </button>
                </td>
              </tr>
              <tr v-if="invoices.length === 0">
                <td colspan="7" class="px-6 py-8 text-center">
                  <svg class="w-12 h-12 mx-auto mb-2" :class="isDarkMode ? 'text-gray-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <p :class="isDarkMode ? 'text-gray-300' : 'text-gray-500'" class="font-medium">No invoices found</p>
                  <p :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" class="text-xs mt-1">Your invoices will appear here after purchasing a plan</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="allInvoices.length > perPage" :class="[
          'px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-4',
          isDarkMode ? 'border-gray-700 bg-gray-700/30' : 'border-gray-100 bg-gray-50/50'
        ]">
          <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm order-2 sm:order-1">
            Showing <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span> to 
            <span class="font-medium">{{ Math.min(currentPage * perPage, allInvoices.length) }}</span> of 
            <span class="font-medium">{{ allInvoices.length }}</span> invoices
          </p>
          <div class="flex gap-2 order-1 sm:order-2">
            <button 
              @click="previousPage" 
              :disabled="currentPage === 1"
              :class="[
                'px-3 py-1 border rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm',
                isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
              ]"
            >
              Previous
            </button>
            <button 
              @click="nextPage" 
              :disabled="currentPage * perPage >= allInvoices.length"
              :class="[
                'px-3 py-1 border rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm',
                isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
              ]"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div 
      v-if="showPaymentModal" 
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" 
      @click.self="closePaymentModal"
    >
      <div :class="[
        'rounded-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto animate-slide-up',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between mb-4">
          <h3 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-bold">Purchase {{ selectedPlan?.name }}</h3>
          <button @click="closePaymentModal" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-400 hover:text-gray-600'" class="transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="mb-6">
          <!-- Plan Summary -->
          <div :class="[
            'rounded-xl p-4 mb-4',
            isDarkMode ? 'bg-gray-700' : 'bg-gradient-to-r from-gray-50 to-gray-100'
          ]">
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Plan</span>
                <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="font-medium">{{ selectedPlan?.name }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Type</span>
                <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="font-medium capitalize">{{ selectedPlan?.type }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Price</span>
                <span class="font-bold text-lg text-blue-600 dark:text-blue-400">${{ formatNumber(selectedPlan?.price) }}</span>
              </div>
              <div v-if="selectedPlan?.type === 'subscription'" class="flex justify-between text-sm">
                <span :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Billing</span>
                <span :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="font-medium capitalize">{{ selectedPlan?.billing_cycle || 'Monthly' }}</span>
              </div>
            </div>
          </div>

          <!-- Payment Method Selection -->
          <div class="mb-4">
            <label :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-sm font-medium block mb-3">Payment Method</label>
            
            <!-- For Subscription: Only Stripe -->
            <div v-if="selectedPlan?.type === 'subscription'" :class="[
              'flex items-center gap-3 p-3 border-2 rounded-xl',
              isDarkMode ? 'bg-purple-900/20 border-purple-700' : 'bg-purple-50 border-purple-300'
            ]">
              <input type="radio" value="stripe" checked disabled class="w-4 h-4 text-purple-600">
              <div class="flex-1">
                <label :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-sm font-medium">Stripe Subscription</label>
                <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">Secure recurring payments</p>
              </div>
              <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
            </div>
            
            <!-- For One-time: Both Cash and Stripe -->
            <div v-else class="space-y-3">
              <div 
                class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all" 
                :class="paymentMethod === 'cash' 
                  ? (isDarkMode ? 'border-green-600 bg-green-900/20' : 'border-green-500 bg-green-50') 
                  : (isDarkMode ? 'border-gray-600 hover:border-gray-500' : 'border-gray-200 hover:border-gray-300')"
                @click="paymentMethod = 'cash'"
              >
                <input type="radio" value="cash" v-model="paymentMethod" class="w-4 h-4 text-green-600">
                <div class="flex-1">
                  <label :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-sm font-medium cursor-pointer">Cash Payment</label>
                  <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">Pay on delivery</p>
                </div>
                <span class="text-xs text-green-600 dark:text-green-400 font-medium">Pay Later</span>
              </div>
              
              <div 
                class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all"
                :class="paymentMethod === 'stripe' 
                  ? (isDarkMode ? 'border-purple-600 bg-purple-900/20' : 'border-purple-500 bg-purple-50') 
                  : (isDarkMode ? 'border-gray-600 hover:border-gray-500' : 'border-gray-200 hover:border-gray-300')"
                @click="paymentMethod = 'stripe'"
              >
                <input type="radio" value="stripe" v-model="paymentMethod" class="w-4 h-4 text-purple-600">
                <div class="flex-1">
                  <label :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'" class="text-sm font-medium cursor-pointer">Stripe Payment</label>
                  <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs">Instant secure payment</p>
                </div>
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Stripe Card Element -->
          <div v-if="paymentMethod === 'stripe'" class="mt-4">
            <div :class="[
              'border-2 rounded-xl p-4 transition-colors focus-within:border-blue-500',
              isDarkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-200 bg-gray-50'
            ]">
              <div id="card-element" class="py-2"></div>
              <div id="card-errors" class="text-red-600 text-xs mt-2" role="alert"></div>
            </div>
            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-xs mt-2 flex items-center gap-1">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              Your payment information is secure and encrypted.
            </p>
          </div>

          <!-- Cash Payment Info -->
          <div v-if="paymentMethod === 'cash'" :class="[
            'mt-4 p-4 rounded-xl border',
            isDarkMode ? 'bg-green-900/20 border-green-700' : 'bg-green-50 border-green-200'
          ]">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div>
                <p :class="isDarkMode ? 'text-green-300' : 'text-green-800'" class="text-sm font-medium">Cash Payment Selected</p>
                <p :class="isDarkMode ? 'text-green-400' : 'text-green-600'" class="text-xs mt-1">Our team will contact you within 24 hours to arrange payment collection. Your plan will be activated after payment confirmation.</p>
              </div>
            </div>
          </div>
        </div>

        <button 
          @click="processPayment" 
          :disabled="processingPayment"
          class="w-full py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          <span v-if="!processingPayment">
            {{ paymentMethod === 'cash' ? 'Confirm Cash Payment' : 'Pay with Stripe' }}
          </span>
          <span v-else class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
          </span>
        </button>
      </div>
    </div>

    <!-- Cancel Subscription Modal -->
    <div 
      v-if="showCancelModal" 
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" 
      @click.self="showCancelModal = false"
    >
      <div :class="[
        'rounded-xl max-w-md w-full p-6 animate-slide-up',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center gap-3 mb-4">
          <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div>
            <h3 :class="isDarkMode ? 'text-white' : 'text-gray-900'" class="text-lg font-bold">Cancel Subscription</h3>
            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'" class="text-sm">This action cannot be undone</p>
          </div>
        </div>
        <p :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'" class="mb-6">
          Are you sure you want to cancel your subscription? You will lose access to premium features at the end of your current billing period.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
          <button 
            @click="showCancelModal = false" 
            :class="[
              'px-4 py-2 border rounded-lg transition-colors text-sm',
              isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'
            ]"
          >
            Keep Subscription
          </button>
          <button @click="confirmCancel" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Yes, Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const loading = ref(false);
    const processingPayment = ref(false);
    const currentSubscription = ref(null);
    const trialMode = ref(false);
    const stripeKey = ref('');
    const trialDaysLeft = ref(0);
    const plans = ref([]);
    const allInvoices = ref([]);
    const showCancelModal = ref(false);
    const showPaymentModal = ref(false);
    const selectedPlan = ref(null);
    const paymentMethod = ref('stripe');
    const currentPage = ref(1);
    const perPage = ref(10);
    const isDarkMode = ref(false);
    const onboarding = ref(false);
    let darkModeObserver = null;
    let stripeInstance = null;
    let cardElement = null;
    let stripeInitialized = false;
    const stripeAccountStatus = ref('not_created');
    const stripeAccountId = ref(null);

    const getSubscriptionStatusClass = (status) => {
      const classes = {
        active: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
        pending: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
        cancelled: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
        expired: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
        trialing: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
      };
      return classes[status?.toLowerCase()] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
    };

    const getInvoiceStatusClass = (status) => {
      const classes = {
        paid: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
        pending: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
        failed: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
        refunded: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
      };
      return classes[status?.toLowerCase()] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
    };

    const formatStatus = (status) => {
      if (!status) return 'N/A';
      return status.charAt(0).toUpperCase() + status.slice(1).toLowerCase();
    };

    const formatNumber = (value) => {
      if (!value) return '0.00';
      return Number(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };

    const formatDate = (date) => {
      if (!date) return 'N/A';
      const d = new Date(date);
      return d.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    };

    const isPlanActive = (planId) => {
      return currentSubscription.value && currentSubscription.value.plan_id === planId && currentSubscription.value.status == 'active';
    };

    const scrollToPlans = () => {
      const element = document.getElementById('plans-section');
      if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    };

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

    const fetchData = async () => {
      loading.value = true;
      try {
        const response = await axios.get('/api/vendor/subscriptions');
        if (response.data.success) {
          const data = response.data.data;
          currentSubscription.value = data.current_subscription;
          trialMode.value = data.trial_mode || false;
          stripeKey.value = data.stripe_key || import.meta.env.VITE_STRIPE_KEY || '';
          trialDaysLeft.value = data.trial_days_left || 0;
          plans.value = data.plans || [];
          allInvoices.value = data.invoices || [];
          stripeAccountStatus.value = data.vendor.subscription_status || 'not_created';
          stripeAccountId.value = data.vendor.stripe_account_id || null;
        }
      } catch (error) {
        console.error('Error fetching data:', error);
        showToast('Failed to load subscription data', 'error');
      } finally {
        loading.value = false;
      }
    };

    const refreshData = () => {
      fetchData();
      showToast('Data refreshed', 'success');
    };

    const selectPlan = (plan) => {
      selectedPlan.value = plan;
      if (plan.type === 'subscription') {
        paymentMethod.value = 'stripe';
      } else {
        paymentMethod.value = 'cash';
      }
      showPaymentModal.value = true;
      setTimeout(() => {
        initializeStripe();
      }, 300);
    };

    const closePaymentModal = () => {
      showPaymentModal.value = false;
      selectedPlan.value = null;
      if (cardElement) {
        cardElement.destroy();
        cardElement = null;
        stripeInitialized = false;
      }
    };

    const initializeStripe = () => {
      if (paymentMethod.value !== 'stripe' || stripeInitialized) return;
      
      if (!stripeKey.value) {
        console.error('Stripe key is missing');
        showToast('Payment system not configured. Please contact support.', 'error');
        return;
      }

      if (typeof Stripe !== 'undefined') {
        try {
          stripeInstance = Stripe(stripeKey.value);
          const elements = stripeInstance.elements();
          const style = {
            base: {
              color: isDarkMode.value ? '#e5e7eb' : '#32325d',
              fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
              fontSmoothing: 'antialiased',
              fontSize: '16px',
              '::placeholder': {
                color: isDarkMode.value ? '#6b7280' : '#aab7c4'
              }
            },
            invalid: {
              color: '#fa755a',
              iconColor: '#fa755a'
            }
          };
          
          const cardElementContainer = document.getElementById('card-element');
          if (cardElementContainer) {
            cardElement = elements.create('card', { style: style });
            cardElement.mount('#card-element');
            stripeInitialized = true;
            
            cardElement.on('change', ({error}) => {
              const displayError = document.getElementById('card-errors');
              if (displayError) {
                if (error) {
                  displayError.textContent = error.message;
                } else {
                  displayError.textContent = '';
                }
              }
            });
          }
        } catch (error) {
          console.error('Error initializing Stripe:', error);
          showToast('Failed to initialize payment system', 'error');
        }
      } else {
        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        script.onload = () => {
          setTimeout(() => initializeStripe(), 500);
        };
        script.onerror = () => {
          showToast('Failed to load payment system. Please refresh the page.', 'error');
        };
        document.head.appendChild(script);
      }
    };

    const processPayment = async () => {
      if (!selectedPlan.value) return;
      
      processingPayment.value = true;
      
      try {
        if (paymentMethod.value === 'cash') {
          const response = await axios.post('/api/vendor/subscriptions/purchase-one-time', {
            plan_id: selectedPlan.value.id,
            payment_method: 'cash'
          });
          
          if (response.data.success) {
            showToast('Plan purchased successfully! Our team will contact you for payment.', 'success');
            closePaymentModal();
            await fetchData();
          }
        } else {
          if (!stripeInstance || !cardElement) {
            showToast('Payment system not initialized. Please try again.', 'error');
            processingPayment.value = false;
            return;
          }
          
          const { error, paymentMethod: stripePaymentMethod } = await stripeInstance.createPaymentMethod({
            type: 'card',
            card: cardElement,
          });
          
          if (error) {
            showToast(error.message, 'error');
            processingPayment.value = false;
            return;
          }
          
          let response;
          if (selectedPlan.value.type === 'subscription') {
            response = await axios.post('/api/vendor/subscriptions/create-subscription', {
              plan_id: selectedPlan.value.id,
              payment_method_id: stripePaymentMethod.id
            });
          } else {
            response = await axios.post('/api/vendor/subscriptions/purchase-one-time', {
              plan_id: selectedPlan.value.id,
              payment_method: 'stripe',
              payment_method_id: stripePaymentMethod.id
            });
          }
          
          if (response.data.success) {
            showToast(selectedPlan.value.type === 'subscription' 
              ? 'Subscription created successfully!' 
              : 'Payment successful! Plan activated.',
              'success'
            );
            closePaymentModal();
            await fetchData();
          }
        }
      } catch (error) {
        console.error('Error processing payment:', error);
        showToast(error.response?.data?.message || 'Payment failed. Please try again.', 'error');
      } finally {
        processingPayment.value = false;
      }
    };

    const cancelSubscription = () => {
      showCancelModal.value = true;
    };

    const confirmCancel = async () => {
      try {
        const response = await axios.post('/api/vendor/subscriptions/cancel');
        if (response.data.success) {
          showToast('Subscription cancelled successfully', 'success');
          showCancelModal.value = false;
          await fetchData();
        }
      } catch (error) {
        console.error('Error cancelling subscription:', error);
        showToast('Failed to cancel subscription', 'error');
      }
    };

    const downloadInvoice = async (invoiceId) => {
      try {
        const response = await axios.get(`/api/vendor/invoices/${invoiceId}/download`, {
          responseType: 'blob'
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `invoice-${invoiceId}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        showToast('Invoice downloaded successfully', 'success');
      } catch (error) {
        console.error('Error downloading invoice:', error);
        showToast('Failed to download invoice', 'error');
      }
    };

    const startOnboarding = async () => {
      onboarding.value = true;
      
      try {
        const response = await axios.post('/api/vendor/stripe/onboarding');
        if (response.data.success) {
          const onboardingUrl = response.data.data.onboarding_url;
          // Redirect to Stripe onboarding
          window.location.href = onboardingUrl;
        } else {
          showToast(response.data.message || 'Failed to start onboarding', 'error');
        }
      } catch (error) {
        console.error('Error starting onboarding:', error);
        showToast(error.response?.data?.message || 'Failed to start onboarding. Please try again.', 'error');
      } finally {
        onboarding.value = false;
      }
    };
   
    const previousPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
      }
    };

    const nextPage = () => {
      if (currentPage.value * perPage.value < allInvoices.value.length) {
        currentPage.value++;
      }
    };

    const invoices = computed(() => {
      const start = (currentPage.value - 1) * perPage.value;
      const end = start + perPage.value;
      return allInvoices.value.slice(start, end);
    });

    // Toast helper
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
        max-width: 90vw;
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

    // Watch for payment method changes
    watch(paymentMethod, () => {
      if (paymentMethod.value === 'stripe' && showPaymentModal.value) {
        setTimeout(() => initializeStripe(), 300);
      } else if (paymentMethod.value !== 'stripe' && stripeInitialized) {
        if (cardElement) {
          cardElement.destroy();
          cardElement = null;
          stripeInitialized = false;
        }
      }
    });

    // Watch for modal visibility
    watch(showPaymentModal, (val) => {
      if (val && paymentMethod.value === 'stripe') {
        setTimeout(() => initializeStripe(), 300);
      }
      if (!val && cardElement) {
        cardElement.destroy();
        cardElement = null;
        stripeInitialized = false;
      }
    });

    // Watch dark mode changes to update Stripe style
    watch(isDarkMode, () => {
      if (cardElement && stripeInitialized) {
        const style = {
          base: {
            color: isDarkMode.value ? '#e5e7eb' : '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: isDarkMode.value ? '#6b7280' : '#aab7c4'
            }
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
          }
        };
        // Recreate card element with new style
        cardElement.destroy();
        const elements = stripeInstance.elements();
        cardElement = elements.create('card', { style: style });
        cardElement.mount('#card-element');
        cardElement.on('change', ({error}) => {
          const displayError = document.getElementById('card-errors');
          if (displayError) {
            if (error) {
              displayError.textContent = error.message;
            } else {
              displayError.textContent = '';
            }
          }
        });
      }
    });

    onMounted(() => {
      checkDarkMode();
      setupDarkModeWatcher();
      fetchData();

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
      if (cardElement) {
        cardElement.destroy();
        cardElement = null;
        stripeInitialized = false;
      }
    });

    return {
      loading,
      processingPayment,
      currentSubscription,
      trialMode,
      stripeKey,
      trialDaysLeft,
      plans,
      invoices,
      allInvoices,
      showCancelModal,
      showPaymentModal,
      selectedPlan,
      paymentMethod,
      currentPage,
      perPage,
      isDarkMode,
      onboarding,
      stripeAccountStatus,
      stripeAccountId,
      getSubscriptionStatusClass,
      getInvoiceStatusClass,
      formatStatus,
      formatNumber,
      formatDate,
      isPlanActive,
      scrollToPlans,
      refreshData,
      selectPlan,
      closePaymentModal,
      processPayment,
      cancelSubscription,
      confirmCancel,
      downloadInvoice,
      startOnboarding,
      previousPage,
      nextPage,
    };
  }
};
</script>

<style scoped>
.subscriptions-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

@media (max-width: 640px) {
  .subscriptions-page {
    padding: 0 0.5rem;
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Line clamp for descriptions */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Scrollbar */
.overflow-x-auto::-webkit-scrollbar {
  height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Dark mode scrollbar */
.dark-mode .overflow-x-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark-mode .overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

/* Stripe dark mode override */
.dark-mode #card-element .Input {
  color: #e5e7eb !important;
}

.dark-mode #card-element .Input::placeholder {
  color: #6b7280 !important;
}
</style>