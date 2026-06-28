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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Vendors</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Vendors</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Manage all vendors on the platform</p>
      </div>
      <router-link 
        to="/admin/vendors/create" 
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-md"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Vendor
      </router-link>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-blue-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Total Vendors</p>
        <p class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ stats.total }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-green-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Active</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.active }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-red-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Inactive</p>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.inactive }}</p>
      </div>
      <div :class="[
        'rounded-2xl shadow-lg p-5 border-l-4 border-yellow-500',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <p class="text-xs uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Active Subscriptions</p>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.active_subscriptions }}</p>
      </div>
    </div>

    <!-- Search -->
    <div :class="[
      'rounded-2xl shadow-lg',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="p-4">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input 
            type="text" 
            v-model="searchTerm" 
            placeholder="Search vendors..."
            :class="[
              'w-full pl-10 pr-4 py-2.5 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
              isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-200 text-gray-900 placeholder-gray-500'
            ]"
          />
        </div>
      </div>
    </div>

    <!-- Vendors Table -->
    <div :class="[
      'rounded-2xl shadow-lg overflow-hidden',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead :class="isDarkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Business</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">User</th>
              <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Orders</th>
              <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Revenue</th>
              <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDarkMode ? 'divide-gray-700' : 'divide-gray-200'">
            <tr v-if="loading" class="text-center">
              <td colspan="6" class="px-4 py-12">
                <div class="flex justify-center items-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                  <span class="ml-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading vendors...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="vendors.length === 0" class="text-center">
              <td colspan="6" class="px-4 py-12" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">No vendors found</td>
            </tr>
            <tr v-for="vendor in vendors" :key="vendor.id" class="transition-colors" :class="isDarkMode ? 'hover:bg-gray-700/50' : 'hover:bg-gray-50'">
              <td class="px-4 py-3">
                <div>
                  <p class="font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ vendor.business_name }}</p>
                  <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">{{ vendor.address || 'No address' }}</p>
                </div>
              </td>
              <td class="px-4 py-3 text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ vendor.user?.name || 'N/A' }}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="vendor.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                  {{ vendor.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ vendor.orders_count || 0 }}</td>
              <td class="px-4 py-3 text-right font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">${{ parseFloat(vendor.revenue || 0).toFixed(2) }}</td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <router-link :to="`/admin/vendors/${vendor.id}`" 
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-blue-400 hover:bg-blue-900/20' : 'text-blue-600 hover:bg-blue-50'"
                    title="View Vendor">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </router-link>
                  <button @click="toggleVendorStatus(vendor)" 
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-yellow-400 hover:bg-yellow-900/20' : 'text-yellow-600 hover:bg-yellow-50'"
                    title="Toggle Status">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                  </button>
                  <button @click="deleteVendor(vendor)" 
                    class="p-1.5 rounded-lg transition-colors"
                    :class="isDarkMode ? 'text-red-400 hover:bg-red-900/20' : 'text-red-600 hover:bg-red-50'"
                    title="Delete Vendor">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
          <button @click="previousPage" :disabled="pagination.current_page <= 1" 
            :class="[
              'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
              isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
            ]">
            Previous
          </button>
          <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ pagination.current_page || 1 }} / {{ pagination.last_page || 1 }}</span>
          <button @click="nextPage" :disabled="pagination.current_page >= pagination.last_page" 
            :class="[
              'px-3 py-1 border rounded-md text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
              isDarkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-white'
            ]">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="closeDeleteModal">
      <div :class="[
        'rounded-2xl shadow-2xl max-w-md w-full p-6 animate-slide-up',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Delete Vendor</h3>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">This action cannot be undone</p>
          </div>
        </div>

        <div class="mb-6">
          <p class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
            Are you sure you want to delete <span class="font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">"{{ vendorToDelete?.business_name }}"</span>?
          </p>
          <p class="text-sm mt-2" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
            This will permanently remove this vendor and all associated data.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3">
          <button @click="closeDeleteModal" 
            :class="[
              'px-4 py-2 rounded-lg transition-colors',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]">
            Cancel
          </button>
          <button @click="confirmDelete" 
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2"
            :disabled="deleting">
            <svg v-if="deleting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="deleting">Deleting...</span>
            <span v-else>Delete Vendor</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

const vendors = ref([]);
const loading = ref(false);
const searchTerm = ref('');
const isDarkMode = ref(false);
const showDeleteModal = ref(false);
const vendorToDelete = ref(null);
const deleting = ref(false);
let darkModeObserver = null;

const pagination = ref({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0
});

const stats = ref({
  total: 0,
  active: 0,
  inactive: 0,
  active_subscriptions: 0
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

const fetchVendors = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/vendors', {
      params: {
        search: searchTerm.value,
        page: pagination.value.current_page,
        per_page: 10
      }
    });
    const data = response.data.data || response.data;
    vendors.value = data.data || data || [];
    
     pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      from: data.from || 0,
      to: data.to || 0,
      total: data.total || 0,
    };
    
    const allVendors = vendors.value;
    stats.value = {
      total: response.data.stats.total,
      active: response.data.stats.active,
      inactive: response.data.stats.inactive,
      active_subscriptions: response.data.stats.active_subscriptions,
    };
  } catch (error) {
    showToast('Failed to load vendors', 'error');
  } finally {
    loading.value = false;
  }
};

const toggleVendorStatus = async (vendor) => {
  try {
    await axios.put(`/api/admin/vendors/${vendor.id}/toggle`);
    showToast(`Vendor ${vendor.is_active ? 'deactivated' : 'activated'} successfully`, 'success');
    fetchVendors();
  } catch (error) {
    showToast('Failed to toggle vendor status', 'error');
  }
};

const deleteVendor = (vendor) => {
  vendorToDelete.value = vendor;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  vendorToDelete.value = null;
  deleting.value = false;
};

const confirmDelete = async () => {
  if (!vendorToDelete.value) return;
  
  deleting.value = true;
  try {
    await axios.delete(`/api/admin/vendors/${vendorToDelete.value.id}`);
    showToast('Vendor deleted successfully', 'success');
    closeDeleteModal();
    fetchVendors();
  } catch (error) {
    showToast('Failed to delete vendor', 'error');
    deleting.value = false;
  }
};

const previousPage = () => {
  if (pagination.value.current_page > 1) {
    pagination.value.current_page--;
    fetchVendors();
  }
};

const nextPage = () => {
  if (pagination.value.current_page < pagination.value.last_page) {
    pagination.value.current_page++;
    fetchVendors();
  }
};

watch(searchTerm, () => {
  pagination.value.current_page = 1;
  fetchVendors();
});

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchVendors();

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
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>