<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        try {
            $user = Auth::user();

            $stats = $this->getVendorStats($user->vendor);

            $recentOrders = $this->getRecentOrders($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_orders' => $recentOrders,
                    'top_products' => $this->getTopProducts(5),
                    'charts' => $this->getChartData()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function getVendorStats($vendor)
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Orders
        $orders = Order::where('vendor_id', $vendor->id);
        $paidOrders = Order::where('vendor_id', $vendor->id)
            ->where('payment_status', 'paid');

        $stats = [
            // Order statistics
            'total_orders' => (clone $orders)->count(),
            'completed_orders' => (clone $orders)->where('status', 'completed')->count(),
            'today_orders' => (clone $orders)->whereDate('created_at', $today)->count(),
            'pending_orders' => (clone $orders)->where('status', 'pending')->count(),
            'processing_orders' => (clone $orders)->where('status', 'processing')->count(),
            'cancelled_orders' => (clone $orders)->where('status', 'cancelled')->count(),

            // Revenue statistics
            'total_revenue' => $paidOrders->sum('total_amount'),
            'today_revenue' => $paidOrders->whereDate('created_at', $today)->sum('total_amount'),
            'this_month_revenue' => $paidOrders->whereBetween('created_at', [$thisMonth, $now])
                ->sum('total_amount'),

            // Commission statistics
            'total_commission' => $paidOrders->sum('commission_amount'),
            'this_month_commission' => $paidOrders->whereBetween('created_at', [$thisMonth, $now])
                ->sum('commission_amount'),
            'commission_rate' => $vendor->commission_rate,

            // Product statistics
            'total_products' => $vendor->products()->count(),
            'active_products' => $vendor->products()->where('is_active', true)->count(),
            'out_of_stock_products' => $vendor->products()
                ->where('stock_quantity', '<=', 0)
                ->count(),
            'low_stock_products' => $vendor->products()
                ->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 10)
                ->count(),

            // Customer statistics
            'unique_customers' => $orders->distinct('customer_id')->count('customer_id'),

            // Average metrics
            'average_order_value' => $orders->count() > 0
                ? round($paidOrders->sum('total_amount') / $orders->count(), 2)
                : 0,

            // Subscription statistics
            'active_subscriptions' => Subscription::where('vendor_id', $vendor->id)
                ->where('status', 'active')
                ->count(),
        ];

        return $stats;
    }

    protected function getRecentOrders($user, $limit = 10)
    {
        $query = Order::with(['vendor', 'customer', 'items'])
            ->orderBy('created_at', 'desc');

        $query->where('vendor_id', $user->vendor->id);

        return $query->limit($limit)->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer->name ?? 'Guest',
                'customer_email' => $order->customer->email ?? '',
                'vendor_name' => $order->vendor->business_name ?? '',
                'total_amount' => number_format($order->total_amount, 2),
                'status' => $order->status,
                'status_badge' => $order->status_badge,
                'payment_status' => $order->payment_status,
                'items_count' => $order->items->count(),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $order->created_at->diffForHumans(),
            ];
        });
    }


    protected function getChartData()
    {
        $data = [];

        $data['sales_last_7_days'] = $this->getSalesByDay(7);

        $data['sales_last_6_months'] = $this->getSalesByMonth(6);

        $data['orders_by_status'] = $this->getOrdersByStatus();

        $data['top_products'] = $this->getTopProducts(5);

        return $data;
    }


    protected function getSalesByDay($days)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $day = $date->format('Y-m-d');

            $total = Order::whereDate('created_at', $day)
                ->where('payment_status', 'paid')
                ->where('vendor_id', 0)
                ->sum('total_amount');

            $count = Order::whereDate('created_at', $day)->where('vendor_id', 0)->count();

            $data[] = [
                'date' => $day,
                'day' => $date->format('D'),
                'total' => number_format($total, 2),
                'orders' => $count
            ];
        }
        return $data;
    }

    protected function getSalesByMonth($months)
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('Y-m');

            $total = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('payment_status', 'paid')
                ->where('vendor_id', Auth::user()->vendor->id)
                ->sum('total_amount');

            $count = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('vendor_id', Auth::user()->vendor->id)
                ->count();

            $data[] = [
                'month' => $month,
                'month_name' => $date->format('M Y'),
                'total' => number_format($total, 2),
                'orders' => $count
            ];
        }
        return $data;
    }

   
    protected function getOrdersByStatus()
    {
        $statuses = ['pending', 'processing', 'completed', 'cancelled', 'refunded', 'shipped', 'delivered'];
        $data = [];

        foreach ($statuses as $status) {
            $count = Order::where('status', $status)->where('vendor_id', Auth::user()->vendor->id)->count();
            if ($count > 0) {
                $data[] = [
                    'status' => $status,
                    'count' => $count,
                    'label' => ucfirst($status)
                ];
            }
        }

        return $data;
    }

    protected function getTopProducts($limit = 5)
    {
        return OrderItem::select(
            'product_id',
            'product_name',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total_price) as total_revenue')
        )
            ->whereHas('order', function ($query) {
                $query->where('payment_status', 'paid')
                    ->where('vendor_id', Auth::user()->vendor->id);
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'total_quantity' => $item->total_quantity,
                    'total_revenue' => number_format($item->total_revenue, 2)
                ];
            });
    }

    protected function calculateGrowth($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }


    public function vendorSettings()
    {
        return response()->json(Auth::user()->vendor);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'nullable|email|max:255',
            'business_phone' => 'nullable|string|max:50',
            'business_website' => 'nullable|url|max:255',
            'business_address' => 'required|string',
            'tax_type' => 'required|in:percentage,fixed',
            'tax_rate' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_rate' => 'required|numeric|min:0',
            'registration_number' => 'nullable|string|max:100',
            'currency' => 'string|max:3',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max
        ]);

        $vendor = Auth::user()->vendor;

        $data = [
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_phone' => $request->business_phone,
            'business_website' => $request->business_website,
            'business_address' => $request->business_address,
            'tax_type' => $request->tax_type,
            'tax_rate' => $request->tax_rate,
            'commission_type' => $request->commission_type,
            'commission_rate' => $request->commission_rate,
            'registration_number' => $request->registration_number,
            'currency' => $request->currency,
        ];

        // Handle logo upload
        if ($request->hasFile('business_logo')) {

            // Delete old logo
            if ($vendor->business_logo && Storage::disk('public')->exists($vendor->business_logo)) {
                Storage::disk('public')->delete($vendor->business_logo);
            }

            $file = $request->file('business_logo');

            $filename = \Illuminate\Support\Str::slug($vendor->business_name ?: 'vendor')
                . '-' . $vendor->id
                . '-' . time()
                . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('vendor-logos', $filename, 'public');

            $data['business_logo'] = $path; // e.g. vendor-logos/my-business-5-1750743215.png
        }

        $vendor->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'business_logo' => $vendor->business_logo ? asset('storage/' . $vendor->business_logo) : null
        ]);
    }
}
