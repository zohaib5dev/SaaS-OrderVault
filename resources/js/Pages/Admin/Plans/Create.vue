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
      <span :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">Add Plan</span>
    </nav>

    <!-- Page Header -->
    <div class="flex items-center gap-4">
      <router-link to="/admin/plans" class="p-2 rounded-lg transition-colors" :class="isDarkMode ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-600'" aria-label="Back to plans">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </router-link>
      <div>
        <h1 class="text-2xl md:text-3xl font-bold" :class="isDarkMode ? 'text-white' : 'text-gray-900'">Create New Plan</h1>
        <p class="text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">Add a new subscription plan to your system</p>
      </div>
    </div>

    <!-- Form -->
    <div :class="[
      'rounded-2xl shadow-lg p-6',
      isDarkMode ? 'bg-gray-800' : 'bg-white'
    ]">
      <form @submit.prevent="createPlan">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Plan Name -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Plan Name <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <input 
                type="text" 
                v-model="form.name" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900',
                  errors.name ? 'border-red-500' : ''
                ]"
                placeholder="Enter plan name (e.g., Basic, Pro, Enterprise)"
              >
            </div>
            <p v-if="errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.name }}</p>
          </div>

          <!-- Price -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Price <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 3v1m2.5 1.5l2.5 2.5M4 16l2.5-2.5" />
              </svg>
              <input 
                type="number" 
                step="0.01" 
                min="0"
                v-model="form.price" 
                :class="[
                  'w-full pl-10 pr-16 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900',
                  errors.price ? 'border-red-500' : ''
                ]"
                placeholder="Enter price (e.g., 29.99)"
              >
              <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-gray-400'">USD</span>
            </div>
            <p v-if="errors.price" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.price }}</p>
            <p class="mt-1 text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Enter the price in USD (e.g., 29.99)
            </p>
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Type <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
              <select v-model="form.type" :class="[
                'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none transition-colors',
                isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'
              ]">
                <option value="">Select type</option>
                <option value="one_time">One Time</option>
                <option value="subscription">Subscription</option>
              </select>
              <svg class="w-5 h-5 absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <p v-if="errors.type" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.type }}</p>
          </div>

          <!-- Price ID -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Price ID
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
              <input 
                type="text" 
                v-model="form.price_id" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="Enter price ID (e.g., price_123456)"
              >
            </div>
            <p class="mt-1 text-xs" :class="isDarkMode ? 'text-gray-400' : 'text-gray-500'">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Price ID from your payment provider (Stripe, etc.)
            </p>
          </div>

          <!-- Description -->
          <div class="col-span-2">
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Description
            </label>
            <div class="relative">
              <svg class="w-5 h-5 absolute left-3 top-3" :class="isDarkMode ? 'text-gray-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
              </svg>
              <textarea 
                v-model="form.description" 
                rows="4" 
                :class="[
                  'w-full pl-10 pr-3 py-2.5 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-y',
                  isDarkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900'
                ]"
                placeholder="Enter plan description"
              ></textarea>
            </div>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium mb-2" :class="isDarkMode ? 'text-gray-300' : 'text-gray-700'">
              Status
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ submitting ? 'Creating...' : 'Create Plan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const submitting = ref(false);
const errors = ref({});
const isDarkMode = ref(false);
let darkModeObserver = null;

const form = reactive({
  name: '',
  price: '',
  type: '',
  price_id: '',
  description: '',
  is_active: true,
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

const createPlan = async () => {
  errors.value = {};
  submitting.value = true;

  try {
    const response = await axios.post('/api/admin/plans', form);
    
    showToast('Plan created successfully', 'success');
    router.push(`/admin/plans/${response.data.data.id}`);
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
      const firstError = document.querySelector('.border-red-500');
      if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    } else {
      showToast(error.response?.data?.message || 'Failed to create plan', 'error');
    }
  } finally {
    submitting.value = false;
  }
};

const goBack = () => {
  router.push('/admin/plans');
};

onMounted(() => {
  checkDarkMode();
  setupDarkModeWatcher();

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

input:focus, textarea:focus {
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