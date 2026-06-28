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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Subscription Invoices</span>
    </nav>

    <!-- Header -->
    <div>
      <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Subscription Invoices</h1>
      <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Manage your subscription invoices</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Invoices</p>
            <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total }}</p>
          </div>
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
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
            <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Paid</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.paid }}</p>
          </div>
          <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-yellow-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Pending</p>
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.pending }}</p>
          </div>
          <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-red-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Overdue</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.overdue }}</p>
          </div>
          <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div :class="[
      'rounded-2xl shadow-lg',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="p-4 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              type="text"
              v-model="searchTerm"
              placeholder="Search by invoice number, status..."
              :class="[
                'w-full pl-10 pr-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-500'
              ]"
            />
          </div>
        </div>
        <div class="flex gap-3 flex-wrap">
          <select
            v-model="statusFilter"
            @change="resetPage"
            :class="[
              'px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'
            ]"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
            <option value="cancelled">Cancelled</option>
            <option value="refunded">Refunded</option>
          </select>
          <select
            v-model="sortBy"
            @change="resetPage"
            :class="[
              'px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'
            ]"
          >
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="amount_high">Amount: High to Low</option>
            <option value="amount_low">Amount: Low to High</option>
          </select>
          <select
            v-model="perPage"
            @change="resetPage"
            :class="[
              'px-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-200 text-gray-900'
            ]"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Invoices Table -->
    <div :class="[
      'rounded-2xl shadow-lg overflow-hidden',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Invoice #
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider hidden sm:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Plan
              </th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Amount
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider hidden md:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Due Date
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Status
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider hidden lg:table-cell" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Payment Method
              </th>
              <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
            <tr v-if="loading" class="text-center">
              <td colspan="7" class="px-4 py-12">
                <div class="flex justify-center items-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                  <span class="ml-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading invoices...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="invoices.length === 0 && !loading" class="text-center">
              <td colspan="7" class="px-4 py-12">
                <svg class="w-16 h-16 mx-auto mb-4" :class="isDarkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-lg font-medium" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">No invoices found</p>
                <p class="text-sm mt-1" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">
                  Try adjusting your search or filter criteria
                </p>
              </td>
            </tr>
            <tr
              v-for="invoice in invoices"
              :key="invoice.id"
              class="transition-colors"
              :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'"
            >
              <td class="px-4 py-3">
                <router-link
                  :to="`/admin/vendors/invoices/${invoice.id}`"
                  class="text-sm font-medium hover:underline"
                  :class="isDarkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-600 hover:text-blue-800'"
                >
                  {{ invoice.invoice_number }}
                </router-link>
              </td>
              <td class="px-4 py-3 hidden sm:table-cell">
                <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ invoice.plan?.name || 'N/A' }}</span>
              </td>
              <td class="px-4 py-3 text-right">
                <span class="text-sm font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">
                  {{ formatCurrency(invoice.amount) }}
                </span>
              </td>
              <td class="px-4 py-3 hidden md:table-cell">
                <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ formatDate(invoice.due_date) }}</span>
              </td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="getStatusBadge(invoice.status)"
                >
                  {{ invoice.status }}
                </span>
              </td>
              <td class="px-4 py-3 hidden lg:table-cell">
                <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ invoice.payment_method || 'N/A' }}</span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <router-link
                    :to="`/admin/vendors/invoices/${invoice.id}`"
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-blue-400 hover:bg-blue-900/20' : 'text-blue-600 hover:bg-blue-50'"
                    title="View Invoice"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </router-link>
                  <button
                    v-if="invoice.status === 'pending' || invoice.status === 'overdue'"
                    @click="openPayModal(invoice)"
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-green-400 hover:bg-green-900/20' : 'text-green-600 hover:bg-green-50'"
                    title="Pay Invoice"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                  </button>
                  <button
                    @click="downloadInvoice(invoice)"
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-50'"
                    title="Download PDF"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 border-t flex flex-col sm:flex-row justify-between items-center gap-4" :class="isDarkMode ? 'border-gray-700 bg-gray-800/50' : 'border-gray-200 bg-gray-50'">
        <div class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-700'">
          Showing <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.from || 0 }}</span> to
          <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.to || 0 }}</span> of
          <span class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ pagination.total || 0 }}</span> results
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="previousPage"
            :disabled="pagination.current_page <= 1"
            :class="[
              'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
              isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
            ]"
          >
            Previous
          </button>
          <div class="flex items-center gap-1">
            <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ pagination.current_page || 1 }}</span>
            <span class="text-sm" :class="isDarkMode ? 'text-gray-500' : 'text-gray-500'">of</span>
            <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ pagination.last_page || 1 }}</span>
          </div>
          <button
            @click="nextPage"
            :disabled="pagination.current_page >= pagination.last_page"
            :class="[
              'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
              isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
            ]"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Pay Invoice Modal -->
    <div v-if="showPayModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75" @click="closePayModal"></div>

        <!-- Modal panel -->
        <div :class="[
          'inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <div :class="[
            'px-4 pt-5 pb-4 sm:p-6 sm:pb-4',
            isDarkMode ? 'bg-gray-800' : 'bg-white'
          ]">
            <!-- Header -->
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'" id="modal-title">
                  Pay Invoice
                </h3>
                <p class="text-sm mt-1" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  Update the status of invoice #{{ selectedInvoice?.invoice_number }}
                </p>
              </div>
              <button
                @click="closePayModal"
                class="transition-colors"
                :class="isDarkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-400 hover:text-gray-500'"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Invoice Summary -->
            <div class="mt-4 p-4 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Invoice Number</p>
                  <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ selectedInvoice?.invoice_number }}</p>
                </div>
                <div>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Amount</p>
                  <p class="text-sm font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatCurrency(selectedInvoice?.amount) }}</p>
                </div>
                <div>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Plan</p>
                  <p class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-900'">{{ selectedInvoice?.plan?.name || 'N/A' }}</p>
                </div>
                <div>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Current Status</p>
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusBadge(selectedInvoice?.status)"
                  >
                    {{ selectedInvoice?.status }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Payment Method Selection -->
            <div class="mt-4">
              <label for="paymentMethod" class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Payment Method
              </label>
              <select
                id="paymentMethod"
                v-model="paymentMethod"
                :class="[
                  'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
                ]"
              >
                <option value="credit_card">Credit Card</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="paypal">PayPal</option>
                <option value="cash">Cash</option>
                <option value="other">Other</option>
              </select>
            </div>

            <!-- Status Update Option -->
            <div class="mt-4">
              <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Update Status To
              </label>
              <div class="flex flex-wrap gap-3">
                <label class="inline-flex items-center cursor-pointer">
                  <input
                    type="radio"
                    v-model="newStatus"
                    value="paid"
                    class="form-radio"
                    :class="isDarkMode ? 'bg-gray-700 border-gray-500' : 'bg-white border-gray-300'"
                  />
                  <span class="ml-2 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Paid</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input
                    type="radio"
                    v-model="newStatus"
                    value="pending"
                    class="form-radio"
                    :class="isDarkMode ? 'bg-gray-700 border-gray-500' : 'bg-white border-gray-300'"
                  />
                  <span class="ml-2 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Pending</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input
                    type="radio"
                    v-model="newStatus"
                    value="cancelled"
                    class="form-radio"
                    :class="isDarkMode ? 'bg-gray-700 border-gray-500' : 'bg-white border-gray-300'"
                  />
                  <span class="ml-2 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Cancelled</span>
                </label>
              </div>
            </div>

            <!-- Notes -->
            <div class="mt-4">
              <label for="paymentNote" class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
                Payment Note (Optional)
              </label>
              <textarea
                id="paymentNote"
                v-model="paymentNote"
                rows="2"
                placeholder="Add any notes about this payment..."
                :class="[
                  'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
              ></textarea>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="mt-4 p-3 rounded-lg border" :class="isDarkMode ? 'bg-red-900/20 border-red-800 text-red-400' : 'bg-red-50 border-red-200 text-red-600'">
              <p class="text-sm">{{ errorMessage }}</p>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <button
              @click="processPayment"
              :disabled="isProcessing"
              class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-base font-medium text-white hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:text-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isProcessing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
              </span>
              <span v-else>Confirm Payment</span>
            </button>
            <button
              @click="closePayModal"
              :disabled="isProcessing"
              :class="[
                'mt-3 w-full sm:w-auto inline-flex justify-center rounded-lg border shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
                isDarkMode ? 'border-gray-600 bg-gray-700 text-gray-300 hover:bg-gray-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
              ]"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const isDarkMode = ref(false);
let darkModeObserver = null;

// Data
const invoices = ref([]);
const loading = ref(false);
const searchTerm = ref('');
const statusFilter = ref('');
const sortBy = ref('newest');
const perPage = ref(10);

// Pagination
const pagination = ref({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0,
});

// Stats
const stats = ref({
  total: 0,
  paid: 0,
  pending: 0,
  overdue: 0,
  total_amount: 0,
  paid_amount: 0,
  pending_amount: 0,
});

// Modal state
const showPayModal = ref(false);
const selectedInvoice = ref(null);
const paymentMethod = ref('credit_card');
const newStatus = ref('paid');
const paymentNote = ref('');
const isProcessing = ref(false);
const errorMessage = ref('');

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

const fetchInvoices = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/vendors/invoices', {
      params: {
        search: searchTerm.value,
        status: statusFilter.value,
        sort: sortBy.value,
        page: pagination.value.current_page,
        per_page: perPage.value,
      },
    });

    const data = response.data.data;
    invoices.value = data.data || [];
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      from: data.from || 0,
      to: data.to || 0,
      total: data.total || 0,
    };
    stats.value = response.data.stats || {};

  } catch (error) {
    console.error('Error fetching invoices:', error);
    showToast('Failed to load invoices', 'error');
  } finally {
    loading.value = false;
  }
};

const getStatusBadge = (status) => {
  const badges = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    overdue: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    cancelled: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    refunded: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
  };
  return badges[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatCurrency = (amount) => {
  if (!amount) return '$0.00';
  return `$${parseFloat(amount).toFixed(2)}`;
};

const openPayModal = (invoice) => {
  selectedInvoice.value = invoice;
  paymentMethod.value = 'credit_card';
  newStatus.value = 'paid';
  paymentNote.value = '';
  errorMessage.value = '';
  showPayModal.value = true;
};

const closePayModal = () => {
  if (isProcessing.value) return;
  showPayModal.value = false;
  selectedInvoice.value = null;
  errorMessage.value = '';
};

const processPayment = async () => {
  if (!selectedInvoice.value) return;

  isProcessing.value = true;
  errorMessage.value = '';

  try {
    const response = await axios.put(
      `/api/admin/vendors/invoices/${selectedInvoice.value.id}/status`,
      {
        status: newStatus.value,
        payment_method: paymentMethod.value,
        payment_note: paymentNote.value,
        paid_at: newStatus.value === 'paid' ? new Date().toISOString() : null,
      }
    );

    const index = invoices.value.findIndex(inv => inv.id === selectedInvoice.value.id);
    if (index !== -1) {
      invoices.value[index] = response.data.data;
    }

    await fetchInvoices();
    showToast(`Invoice ${selectedInvoice.value.invoice_number} updated to ${newStatus.value}`, 'success');
    closePayModal();

  } catch (error) {
    console.error('Error updating invoice status:', error);
    errorMessage.value = error.response?.data?.message || 'Failed to update invoice status. Please try again.';
    showToast('Failed to update invoice', 'error');
  } finally {
    isProcessing.value = false;
  }
};

const downloadInvoice = async (invoice) => {
  try {
    const response = await axios.get(`/api/admin/vendors/invoices/${invoice.id}/download`, {
      responseType: 'blob'
    });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `invoice-${invoice.id}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    showToast('Invoice downloaded successfully', 'success');
  } catch (error) {
    console.error('Error downloading invoice:', error);
    showToast('Failed to download invoice', 'error');
  }
};

const previousPage = () => {
  if (pagination.value.current_page > 1) {
    pagination.value.current_page--;
    fetchInvoices();
  }
};

const nextPage = () => {
  if (pagination.value.current_page < pagination.value.last_page) {
    pagination.value.current_page++;
    fetchInvoices();
  }
};

const resetPage = () => {
  pagination.value.current_page = 1;
  fetchInvoices();
};

watch([searchTerm, statusFilter, sortBy, perPage], () => {
  resetPage();
});

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchInvoices();

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