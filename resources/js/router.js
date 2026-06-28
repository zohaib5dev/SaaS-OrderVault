import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from './store';

const routes = [
    {
        path: '/',
        name: '',
        component: () => import('./Pages/Auth/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('./Pages/Auth/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('./Pages/Auth/Register.vue'),
        meta: { guest: true }
    },

    {
        path: '/customer',
        component: () => import('./Layouts/CustomerLayout.vue'),
        meta: { requiresAuth: true, role: 'customer' },
        children: [
            {
                path: 'dashboard',
                name: 'CustomerDashboard',
                component: () => import('./Pages/Customer/Dashboard.vue')
            },
            {
                path: 'profile',
                name: 'CustomerProfile',
                component: () => import('./Pages/Profile/Index.vue')
            },
            {
                path: 'orders/:id',
                name: 'CustomerOrder',
                component: () => import('./Pages/Customer/Order.vue')
            },
            {
                path: 'orders/:id/payment',
                name: 'CustomerOrderPayment',
                component: () => import('./Pages/Customer/Payment.vue')
            },
        ]
    },

    {
        path: '/vendor',
        component: () => import('./Layouts/VendorLayout.vue'),
        meta: { requiresAuth: true, role: 'vendor' },
        children: [
            {
                path: 'dashboard',
                name: 'VendorDashboard',
                component: () => import('./Pages/Vendor/Dashboard.vue')
            },
            {
                path: 'settings',
                name: 'VendorSettings',
                component: () => import('./Pages/Vendor/Settings.vue')
            },
            {
                path: 'profile',
                name: 'VendorProfile',
                component: () => import('./Pages/Profile/Index.vue')
            },
            {
                path: 'products',
                name: 'VendorProducts',
                component: () => import('./Pages/Vendor/Products/Index.vue')
            },
            {
                path: 'products/create',
                name: 'VendorProductCreate',
                component: () => import('./Pages/Vendor/Products/Create.vue')
            },
            {
                path: 'products/:id',
                name: 'VendorProductshow',
                component: () => import('./Pages/Vendor/Products/Show.vue')
            },
            {
                path: 'products/:id/edit',
                name: 'VendorProductEdit',
                component: () => import('./Pages/Vendor/Products/Edit.vue')
            },
            {
                path: 'orders',
                name: 'VendorOrders',
                component: () => import('./Pages/Vendor/Orders/Index.vue')
            },
            {
                path: 'orders/create',
                name: 'VendorOrderCreate',
                component: () => import('./Pages/Vendor/Orders/Create.vue')
            },
            {
                path: 'orders/:id',
                name: 'VendorOrderShow',
                component: () => import('./Pages/Vendor/Orders/Show.vue')
            },
            {
                path: 'orders/:id/edit',
                name: 'VendorOrderEdit',
                component: () => import('./Pages/Vendor/Orders/Edit.vue')
            },
            {
                path: 'customers',
                name: 'VendorCustomers',
                component: () => import('./Pages/Vendor/Customers/Index.vue')
            },
            {
                path: 'customers/:id',
                name: 'VendorCustomerShow',
                component: () => import('./Pages/Vendor/Customers/Show.vue')
            },
            {
                path: 'analytics',
                name: 'VendorAnalytics',
                component: () => import('./Pages/Vendor/Analytics.vue')
            },
            {
                path: 'subscription',
                name: 'VendorSubscription',
                component: () => import('./Pages/Vendor/Subscription.vue')
            },
            {
                path: 'categories',
                name: 'VendorCategories',
                component: () => import('./Pages/Vendor/Categories.vue')
            },


        ]
    },

    {
        path: '/admin',
        component: () => import('./Layouts/AdminLayout.vue'),
        meta: { requiresAuth: true, role: 'admin' },
        children: [
            {
                path: 'dashboard',
                name: 'AdminDashboard',
                component: () => import('./Pages/Admin/Dashboard.vue')
            },
            {
                path: 'settings',
                name: 'AdminSettings',
                component: () => import('./Pages/Admin/Settings.vue')
            },
            {
                path: 'vendor/settings',
                name: 'AdminVendorSettings',
                component: () => import('./Pages/Admin/VendorSettings.vue')
            },
            {
                path: 'profile',
                name: 'AdminProfile',
                component: () => import('./Pages/Profile/Index.vue')
            },
            {
                path: 'vendors',
                name: 'AdminVendors',
                component: () => import('./Pages/Admin/Vendors/Index.vue')
            },
            {
                path: 'vendors/create',
                name: 'AdminVendorCreate',
                component: () => import('./Pages/Admin/Vendors/Create.vue')
            },
            {
                path: 'vendors/:id',
                name: 'AdminVendorShow',
                component: () => import('./Pages/Admin/Vendors/Show.vue'),
                props: true
            },
            {
                path: 'vendors/:id/edit',
                name: 'AdminVendorEdit',
                component: () => import('./Pages/Admin/Vendors/Edit.vue')
            },
            {
                path: 'products/all',
                name: 'AdminAllProducts',
                component: () => import('./Pages/Admin/Products/AllIndex.vue')
            },
            {
                path: 'products',
                name: 'AdminProducts',
                component: () => import('./Pages/Admin/Products/Index.vue')
            },
            {
                path: 'products/create',
                name: 'AdminProductCreate',
                component: () => import('./Pages/Admin/Products/Create.vue')
            },
            {
                path: 'products/:id',
                name: 'AdminProductshow',
                component: () => import('./Pages/Admin/Products/Show.vue')
            },
            {
                path: 'products/:id/edit',
                name: 'AdminProductEdit',
                component: () => import('./Pages/Admin/Products/Edit.vue')
            },
            {
                path: 'orders',
                name: 'AdminOrders',
                component: () => import('./Pages/Admin/Orders/Index.vue')
            },
            {
                path: 'orders/all',
                name: 'AdminAllOrders',
                component: () => import('./Pages/Admin/Orders/AllIndex.vue')
            },
            {
                path: 'orders/create',
                name: 'AdminOrderCreate',
                component: () => import('./Pages/Admin/Orders/Create.vue')
            },
            {
                path: 'orders/:id',
                name: 'AdminOrderShow',
                component: () => import('./Pages/Admin/Orders/Show.vue')
            },
            {
                path: 'orders/:id/edit',
                name: 'AdminOrderEdit',
                component: () => import('./Pages/Admin/Orders/Edit.vue')
            },
            {
                path: 'customers',
                name: 'AdminCustomers',
                component: () => import('./Pages/Admin/Customers/Index.vue')
            },
            {
                path: 'customers/create',
                name: 'AdminCustomersCreate',
                component: () => import('./Pages/Admin/Customers/Create.vue')
            },
            {
                path: 'customers/:id',
                name: 'AdminCustomerShow',
                component: () => import('./Pages/Admin/Customers/Show.vue')
            },
            {
                path: 'customers/:id/edit',
                name: 'AdminCustomerEdit',
                component: () => import('./Pages/Admin/Customers/Edit.vue')
            },
            {
                path: 'analytics/orders',
                name: 'AdminAnalytics',
                component: () => import('./Pages/Admin/Analytics.vue')
            },
            {
                path: 'analytics/vendors/orders',
                name: 'AdminVendorsAnalytics',
                component: () => import('./Pages/Admin/VendorsAnalytics.vue')
            },
            {
                path: 'analytics/plans',
                name: 'AdminPlansAnalytics',
                component: () => import('./Pages/Admin/PlansAnalytics.vue')
            },
            {
                path: 'plans',
                name: 'AdminPlans',
                component: () => import('./Pages/Admin/Plans/Index.vue')
            },
            {
                path: 'plans/create',
                name: 'AdminPlansCreate',
                component: () => import('./Pages/Admin/Plans/Create.vue')
            },
            {
                path: 'plans/:id',
                name: 'AdminPlanshow',
                component: () => import('./Pages/Admin/Plans/Show.vue')
            },
            {
                path: 'plans/:id/edit',
                name: 'AdminPlanEdit',
                component: () => import('./Pages/Admin/Plans/Edit.vue')
            },
            {
                path: 'vendors/invoices',
                name: 'AdminInvoices',
                component: () => import('./Pages/Admin/Invoices/Index.vue')
            },
            {
                path: 'vendors/invoices/:id',
                name: 'AdminInvoiceshow',
                component: () => import('./Pages/Admin/Invoices/Show.vue')
            },
            {
                path: 'categories',
                name: 'AdminCategories',
                component: () => import('./Pages/Admin/Categories/Index.vue')
            },
            {
                path: 'categories/all',
                name: 'AdminVendorCategories',
                component: () => import('./Pages/Admin/Categories/AllIndex.vue')
            },

        ]
    },

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;
    const userRole = authStore.user?.role;


    // Check if route requires authentication
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
        return;
    }

    // Check if route has role restrictions
    if (to.meta.requiresAuth && to.meta.role) {
        const allowedRoles = Array.isArray(to.meta.role) ? to.meta.role : [to.meta.role];

        // Map super_admin to admin for routing purposes
        const normalizedUserRole = userRole === 'super_admin' ? 'admin' : userRole;

        if (!allowedRoles.includes(normalizedUserRole) && !allowedRoles.includes(userRole)) {
            // Redirect to appropriate dashboard based on role
            if (userRole === 'super_admin' || userRole === 'admin') {
                next('/admin/dashboard');
            } else if (userRole === 'vendor') {
                next('/vendor/dashboard');
            } else if (userRole === 'customer') {
                next('/customer/dashboard');
            } else {
                next('/');
            }
            return;
        }
    }

    // Redirect guest users away from auth pages
    if (to.meta.guest && isAuthenticated) {
        const userRole = authStore.user?.role;
        if (userRole === 'super_admin' || userRole === 'admin') {
            next('/admin/dashboard');
        } else if (userRole === 'vendor') {
            next('/vendor/dashboard');
        } else if (userRole === 'customer') {
            next('/customer/dashboard');
        } else {
            next('/');
        }
        return;
    }

    next();
});

export default router;