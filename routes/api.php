
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VendorController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Vendor\AnalyticsController;
use App\Http\Controllers\Vendor\CustomerController as VendorCustomerController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Vendor\ProductController as VendorProductController;


use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PlanAnalyticsController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Admin\VendorsAnalyticsController;
use App\Http\Controllers\Vendor\CategoryController as VendorCategoryController;
use App\Http\Controllers\Vendor\SubscriptionController;

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Orders

    Route::post('orders/bulk-status', [OrderController::class, 'bulkUpdateStatus']);
    Route::get('orders/export', [OrderController::class, 'export']);

    // Products
    Route::get('products/filter', [ProductController::class, 'filter']);



    // Vendor Panel
    Route::get('vendor/subscription/status', [SubscriptionController::class, 'status']);
    // Subscription Management
    Route::get('vendor/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('vendor/subscriptions/create-subscription', [SubscriptionController::class, 'createSubscription']);
    Route::post('vendor/subscriptions/purchase-one-time', [SubscriptionController::class, 'purchaseOneTime']);
    Route::post('vendor/subscriptions/cancel', [SubscriptionController::class, 'cancel']);

    Route::middleware(['role:vendor', 'vendorSubscription'])->prefix('vendor')->group(function () {

        Route::post('stripe/onboarding', [SubscriptionController::class, 'createStripeAccount']);
        
        Route::get('dashboard', [VendorDashboardController::class, 'stats']);
        Route::apiResource('customers', VendorCustomerController::class);
        Route::get('categories/active', [VendorCategoryController::class, 'getActive']);
        Route::apiResource('categories', VendorCategoryController::class);
        Route::get('orders/{id}/download', [VendorOrderController::class, 'downloadInvoice']);
        Route::apiResource('orders', VendorOrderController::class);
        Route::apiResource('products', VendorProductController::class);
        Route::get('analytics', [AnalyticsController::class, 'analytics']);
        Route::get('settings', [VendorDashboardController::class, 'vendorSettings']);
        Route::put('settings', [VendorDashboardController::class, 'updateSettings']);


        // Invoice Management
        Route::get('invoices/{id}/download', [SubscriptionController::class, 'downloadInvoice']);

        // Webhook for Stripe (no auth needed)
        Route::post('stripe/webhook', [SubscriptionController::class, 'handleWebhook']);
    });



    // Admin Panel
    Route::get('admin/dashboard', [AdminDashboardController::class, 'index']);
    Route::apiResource('admin/customers', AdminCustomerController::class);
    Route::get('admin/plans/analytics', [PlanAnalyticsController::class, 'index']);
    Route::apiResource('admin/plans', PlanController::class);

    Route::get('admin/orders/{id}/download', [AdminOrderController::class, 'downloadInvoice']);
    Route::apiResource('admin/orders', AdminOrderController::class);

    Route::get('admin/vendors/analytics', [VendorsAnalyticsController::class, 'index']);

    Route::get('admin/vendors/invoices/{id}', [InvoiceController::class, 'show']);
    Route::put('admin/vendors/invoices/{id}/status', [InvoiceController::class, 'updateStatus']);
    Route::get('admin/vendors/invoices/{id}/download', [InvoiceController::class, 'downloadInvoice']);
    Route::get('admin/vendors/invoices', [InvoiceController::class, 'index']);

    Route::put('admin/vendors/{id}/toggle', [AdminVendorController::class, 'toggleStatus']);

    Route::apiResource('admin/vendors', AdminVendorController::class);

    Route::apiResource('admin/products', AdminProductController::class);
    Route::get('admin/categories/active', [CategoryController::class, 'getActive']);
    Route::apiResource('admin/categories', CategoryController::class);
    Route::get('admin/analytics', [AnalyticsController::class, 'analytics']);

    Route::get('admin/vendor/settings', [AdminDashboardController::class, 'vendorSettings']);
    Route::put('admin/vendor/settings', [AdminDashboardController::class, 'updateSettings']);

    Route::get('admin/settings', [SettingsController::class, 'index']);
    Route::post('admin/settings', [SettingsController::class, 'store']);
    Route::put('admin/settings/{key}', [SettingsController::class, 'update']);
    Route::patch('admin/settings/{key}/toggle', [SettingsController::class, 'toggle']);
    Route::delete('admin/settings/{key}', [SettingsController::class, 'destroy']);
    Route::post('admin/settings/logo', [SettingsController::class, 'uploadLogo']);


    Route::get('user/profile', [ProfileController::class, 'show']);
    Route::put('user/profile', [ProfileController::class, 'update']);
    Route::post('user/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::post('user/profile/password', [ProfileController::class, 'updatePassword']);



    Route::get('customer/dashboard', [CustomerController::class, 'dashboard']);
    Route::get('customer/orders/{id}', [CustomerController::class, 'getOrder']);
    Route::get('customer/orders/{id}/download', [CustomerController::class, 'downloadInvoice']);
    Route::post('customer/orders/{id}/pay', [CustomerController::class, 'payInvoice']);
});

// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('register', [AuthController::class, 'register']);
