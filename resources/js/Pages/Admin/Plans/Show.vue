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
      <router-link to="/admin/plans" :class="isDarkMode ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        Plans
      </router-link>
      <span :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'">/</span>
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">{{ plan?.name || 'Plan Details' }}</span>
    </nav>

    <!-- Loading State -->
    <div v-if="loading" :class="[
      'rounded-2xl shadow-lg p-12 text-center',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <div class="flex flex-col items-center justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Loading plan details...</p>
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
      <!-- Plan Header -->
      <div :class="[
        'rounded-2xl shadow-lg p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-start gap-4">
          <router-link to="/admin/plans" class="p-2 rounded-lg transition-colors flex-shrink-0" :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'" aria-label="Back to plans">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
          </router-link>
          <div class="flex-1 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg flex-shrink-0">
                {{ getInitials(plan.name) }}
              </div>
              <div>
                <h1 class="text-2xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-800'">{{ plan.name }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-1">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="plan.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'">
                    {{ plan.is_active ? 'Active' : 'Inactive' }}
                  </span>
                  <span class="text-sm flex items-center" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Created: {{ formatDate(plan.created_at) }}
                  </span>
                  <span class="text-sm flex items-center" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Updated: {{ formatDate(plan.updated_at) }}
                  </span>
                </div>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
              <router-link
                :to="`/admin/plans/${plan.id}/edit`"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 from-indigo-500 to-indigo-600 text-white rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition-all duration-200 shadow-md"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Plan
              </router-link>
              <button
                @click="deletePlan"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors shadow-md"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Plan Details -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Info Card -->
        <div :class="[
          'rounded-2xl shadow-lg p-6',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            <svg class="w-5 h-5 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Plan Information
          </h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Plan Name</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ plan.name }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Price</span>
              <code class="text-sm px-2 py-1 rounded" :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'">{{ plan.price || 'N/A' }}</code>
            </div>
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Type</span>
              <span class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ plan.type || 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Price ID</span>
              <code class="text-sm px-2 py-1 rounded" :class="isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'">{{ plan.price_id || 'N/A' }}</code>
            </div>
            <div class="flex justify-between items-center py-2 border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-100'">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Status</span>
              <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="plan.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'">
                {{ plan.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <div class="flex justify-between items-center py-2">
              <span class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">ID</span>
              <span class="text-sm" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">#{{ plan.id }}</span>
            </div>
          </div>
        </div>

        <!-- Description Card -->
        <div :class="[
          'rounded-2xl shadow-lg p-6',
          isDarkMode ? 'bg-gray-800' : 'bg-white'
        ]">
          <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
            <svg class="w-5 h-5 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            Description
          </h3>
          <div class="prose prose-sm max-w-none" :class="isDarkMode ? 'prose-invert' : ''">
            <p class="leading-relaxed" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">
              {{ plan.description || 'No description provided.' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Timestamps -->
      <div :class="[
        'rounded-2xl shadow-lg p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <h3 class="text-lg font-semibold mb-4 flex items-center" :class="isDarkMode ? 'text-white' : 'text-gray-800'">
          <svg class="w-5 h-5 mr-2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Timestamps
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="flex items-center gap-3 p-3 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <div>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Created At</p>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatDate(plan.created_at) }}</p>
            </div>
          </div>
          <div class="flex items-center gap-3 p-3 rounded-lg" :class="isDarkMode ? 'bg-gray-700' : 'bg-gray-50'">
            <svg class="w-5 h-5" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <div>
              <p class="text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">Last Updated</p>
              <p class="text-sm font-medium" :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ formatDate(plan.updated_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
      @click.self="closeDeleteModal"
    >
      <div :class="[
        'rounded-2xl shadow-2xl max-w-md w-full p-6',
        isDarkMode ? 'bg-gray-800' : 'bg-white'
      ]">
        <div class="flex items-center gap-3 mb-4">
          <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Delete Plan</h3>
            <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">This action cannot be undone.</p>
          </div>
        </div>

        <p class="mb-4" :class="isDarkMode ? 'text-gray-300' : 'text-gray-600'">
          Are you sure you want to delete <strong :class="isDarkMode ? 'text-white' : 'text-gray-900'">{{ plan?.name }}</strong>?
        </p>

        <div class="flex justify-end gap-3">
          <button
            @click="closeDeleteModal"
            :class="[
              'px-4 py-2 rounded-lg transition-colors',
              isDarkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
            ]"
          >
            Cancel
          </button>
          <button
            @click="confirmDelete"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            Delete Plan
          </button>
        </div>
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

const loading = ref(true);
const error = ref(null);
const plan = ref({
  id: null,
  name: '',
  price: '',
  type: '',
  price_id: '',
  description: '',
  is_active: true,
  created_at: null,
  updated_at: null,
});
const showDeleteModal = ref(false);
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

const fetchPlan = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await axios.get(`/api/admin/plans/${route.params.id}`);
    plan.value = response.data.data;

  } catch (err) {
    console.error('Error fetching plan:', err);
    error.value = err.response?.data?.message || 'Failed to load plan data';
  } finally {
    loading.value = false;
  }
};

const deletePlan = () => {
  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  try {
    await axios.delete(`/api/admin/plans/${plan.value.id}`);
    showToast('Plan deleted successfully', 'success');
    router.push('/admin/plans');
  } catch (error) {
    console.error('Error deleting plan:', error);
    showToast(error.response?.data?.message || 'Failed to delete plan', 'error');
    closeDeleteModal();
  }
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
};

const goBack = () => {
  router.push('/admin/plans');
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

const getInitials = (name) => {
  if (!name) return '?';
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();
  fetchPlan();

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
.prose {
  max-width: 100%;
}

.prose p {
  margin: 0;
}

button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

/* Modal animation */
.fixed {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
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