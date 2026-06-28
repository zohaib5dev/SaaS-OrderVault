<template>
  <div class="space-y-6 pb-8">
   <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
      <router-link to="/admin/dashboard" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      </router-link>
            <router-link to="/admin/vendors/invoices" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
            Subscription Invoices
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ invoice.invoice_number }}</span>
    </nav>


    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading invoice details...</p>
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

    <!-- Content -->
    <div v-else>
      <!-- Invoice Header -->
      <div :class="[
        'rounded-2xl shadow-lg p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg flex-shrink-0">
              {{ getInitials(invoice.invoice_number) }}
            </div>
            <div>
              <h1 class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">{{ invoice.invoice_number }}</h1>
              <div class="flex flex-wrap items-center gap-3 mt-1">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusBadge(invoice.status)">
                  {{ invoice.status }}
                </span>
                <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  {{ invoice.plan?.name }}
                </span>
                <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                  Created: {{ formatDate(invoice.created_at) }}
                </span>
              </div>
            </div>
          </div>
          <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <button
              v-if="invoice.status === 'pending' || invoice.status === 'overdue'"
              @click="payInvoice"
              class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
              Pay Now
            </button>
            <button
              @click="downloadInvoice(invoice)"
              class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Download PDF
            </button>
          </div>
        </div>
      </div>

      <!-- Invoice Details Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 my-3 gap-6">
        <!-- Invoice Information -->
        <div :class="[
          'rounded-2xl shadow-lg p-6',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            <svg class="w-5 h-5 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Invoice Information
          </h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Invoice Number</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ invoice.invoice_number }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Plan</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ invoice.plan?.name || 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</span>
              <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusBadge(invoice.status)">
                {{ invoice.status }}
              </span>
            </div>
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Due Date</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatDate(invoice.due_date) }}</span>
            </div>
            <div v-if="invoice.paid_at" class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Paid Date</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatDate(invoice.paid_at) }}</span>
            </div>
            <div class="flex justify-between items-center py-2">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Payment Method</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ invoice.payment_method || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <!-- Amount Summary -->
        <div :class="[
          'rounded-2xl shadow-lg p-6',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            <svg class="w-5 h-5 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 3v1m2.5 1.5l2.5 2.5M4 16l2.5-2.5" />
            </svg>
            Amount Summary
          </h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Subtotal</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatCurrency(invoice.subtotal || invoice.amount) }}</span>
            </div>
            <div v-if="invoice.tax" class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Tax</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatCurrency(invoice.tax) }}</span>
            </div>
            <div v-if="invoice.discount" class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Discount</span>
              <span class="text-sm font-medium text-red-600 dark:text-red-400">-{{ formatCurrency(invoice.discount) }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-t-2" :class="isDarkMode ? 'border-gray-600' : 'border-gray-200'">
              <span class="text-base font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">Total</span>
              <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ formatCurrency(invoice.amount) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Items Table -->
      <div v-if="invoice.items && invoice.items.length > 0" :class="[
        'rounded-2xl shadow-lg p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
          <svg class="w-5 h-5 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          Invoice Items
        </h3>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Description</th>
                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Quantity</th>
                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Unit Price</th>
                <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
              <tr v-for="(item, index) in invoice.items" :key="index" class="transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
                <td class="px-4 py-2 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ item.description || item.name }}</td>
                <td class="px-4 py-2 text-sm text-right" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ item.quantity || 1 }}</td>
                <td class="px-4 py-2 text-sm text-right" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ formatCurrency(item.unit_price || item.price) }}</td>
                <td class="px-4 py-2 text-sm font-medium text-right" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatCurrency((item.quantity || 1) * (item.unit_price || item.price)) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Notes -->
      <div v-if="invoice.notes" :class="[
        'rounded-2xl shadow-lg p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
          <svg class="w-5 h-5 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
          </svg>
          Notes
        </h3>
        <p class="leading-relaxed" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">{{ invoice.notes }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const isDarkMode = ref(false);
let darkModeObserver = null;

const loading = ref(true);
const error = ref(null);
const invoice = ref({});

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

const fetchInvoice = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await axios.get(`/api/admin/vendors/invoices/${route.params.id}`);
    invoice.value = response.data.data;

  } catch (err) {
    console.error('Error fetching invoice:', err);
    error.value = err.response?.data?.message || 'Failed to load invoice details';
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
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatCurrency = (amount) => {
  if (!amount) return '$0.00';
  return `$${parseFloat(amount).toFixed(2)}`;
};

const getInitials = (text) => {
  if (!text) return '?';
  return text
    .split('-')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

const goBack = () => {
  router.push('/admin/invoices');
};

const payInvoice = () => {
  router.push(`/admin/invoices/${invoice.value.id}/pay`);
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

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchInvoice();

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