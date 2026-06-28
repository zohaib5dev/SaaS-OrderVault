<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        // Get orders based on user role
        if ($user->isAdmin()) {
            $orders = Order::with(['vendor', 'customer', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } elseif ($user->isVendor()) {
            $orders = Order::with(['customer', 'items.product'])
                ->where('vendor_id', $user->vendor->id)
                ->orderBy('created_at', 'desc');
        } else {
            $orders = Order::with(['vendor', 'items.product'])
                ->where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        $perPage = $request->get('per_page', 10);
        $orders = $orders->paginate($perPage);
$stats = [
            'total_orders' => Order::count(),
            'total_paid_orders' => Order::where('payment_status', 'paid')->count(),
            'total_pending_orders' => Order::where('status', 'pending')->count(),
            'total_completed_orders' => Order::where('status', 'completed')->count(),
        ];
 

        return response()->json(['orders' => $orders, 'stats' => $stats]);
        
    }


    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'payment_method' => 'required|string',
            'payment_status' => 'nullable|in:pending,paid,failed',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $vendor = $user->vendor;

            // Calculate totals
            $subtotal = 0;
            $commissionAmount = 0;
            $totalDiscount = 0;
            $orderItems = [];

            foreach ($request->items as $itemData) {
                $product = Product::find($itemData['product_id']);

                // Check stock
                if ($product->stock_quantity < $itemData['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $itemTotal = $itemData['price'] * $itemData['quantity'];
                $discountAmount = ($itemTotal * ($itemData['discount'] ?? 0)) / 100;
                $totalPrice = $itemTotal - $discountAmount;

                $subtotal += $itemTotal;
                $totalDiscount += $discountAmount;

                $orderItems[] = [
                    'product_id' => $itemData['product_id'],
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'total_price' => $totalPrice,
                    'discount' => $itemData['discount'],
                ];
            }

            // Calculate commission (vendor commission)
            if ($vendor->commission_type == 'percentage') {
                $commissionAmount = ($request->total * ($vendor->commission_rate ?? 0)) / 100;
            } else {
                $commissionAmount = $vendor->commission_rate ?? 0;
            }

            $totalAmount = $request->total + $commissionAmount;
            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(8));

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'vendor_id' => $vendor->id,
                'customer_id' => $request->customer_id,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address ?? $request->shipping_address,
                'subtotal' => $subtotal,
                'tax_amount' => $request->tax_amount,
                'shipping_cost' => $request->shipping_cost,
                'discount_amount' => $totalDiscount,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'currency' => 'usd',
                'status' => 'pending',
                'due_at' => $request->due_date,
                'payment_status' => $request->payment_status ?? 'pending',
                'fulfillment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'metadata' => [
                    'created_by' => $user->id,
                    'vendor_name' => $vendor->business_name,
                    'customer_email' => User::find($request->customer_id)->email,
                    'items_count' => count($orderItems)
                ]
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'discount_amount' => $item['discount'] ?? 0,
                    'total_price' => $item['total_price'],
                ]);

                // Reduce stock
                Product::where('id', $item['product_id'])->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            return response()->json(['success' => true, 'data' => $order]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'data' => $e->getMessage()]);
        }
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        // Check authorization
        if (!$user->isAdmin() && $user->id != $order->customer_id && $user->vendor?->id != $order->vendor_id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['vendor', 'customer', 'items.product']);

        return response($order);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded,partially_paid',
            // 'fulfillment_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $newStatus = $request->status;

            // Update order
            $order->update([
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'shipping_address' => $request->shipping_address ?? $order->shipping_address,
                'billing_address' => $request->billing_address ?? $order->billing_address,
                'notes' => $request->notes ?? $order->notes,
            ]);

            // Update timestamps based on status
            if ($request->status === 'paid' && !$order->paid_at) {
                $order->update(['paid_at' => now()]);
            }

            if ($request->status === 'shipped' && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            }



            // If order is cancelled, restore stock
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }
            }

            // If order is refunded, update payment status
            if ($request->status === 'refunded') {
                $order->update(['payment_status' => 'refunded']);
            }

            DB::commit();

            return response()->json([
                'success' => true,

            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()

            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $user = Auth::user();

        // Check authorization
        if (!$user->isAdmin() && $user->vendor?->id != $order->vendor_id) {
            abort(403, 'You can only delete your own orders.');
        }

        try {
            DB::beginTransaction();

            // Check if order can be deleted
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                throw new \Exception('Only pending or cancelled orders can be deleted.');
            }

            // Restore stock
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
            }

            // Delete order items
            $order->items()->delete();

            // Delete order
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    /**
     * Update order status (quick status change)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded',
        ]);

        $user = Auth::user();

        // Check authorization
        if (!$user->isAdmin() && $user->vendor?->id != $order->vendor_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $newStatus = $request->status;

            $order->update(['status' => $newStatus]);

            // Update timestamps
            if ($newStatus === 'paid' && !$order->paid_at) {
                $order->update(['paid_at' => now()]);
            }

            if ($newStatus === 'shipped' && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            }

            if ($newStatus === 'delivered' && !$order->delivered_at) {
                $order->update(['delivered_at' => now()]);
            }

            // If order is cancelled, restore stock
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order status updated to ' . ucfirst($newStatus));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }


    public function downloadInvoice(Request $request, $id)
    {
        $order = Order::with(['customer', 'items.product'])->findOrFail($id);

        $vendor = Auth::user()->vendor;
        $logoPath = null;
        if ($vendor->business_logo) {
            $logoPath = public_path('storage/' . $vendor->business_logo);
        }
        $data = [
            'invoice' => [
                'id' => $order->id,
                'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'date' => $order->created_at->format('F d, Y'),
            ],
            'vendor' => Auth::user()->vendor,
            'logoPath' => $logoPath,
            'order' => $order->toArray(),
            'customer' => $order->customer->toArray(),
            'items' => $order->items->toArray(),
            'shipping_address' => $order->shipping_address,
            'billing_address' => $order->billing_address,
            'company' => [
                'name' => config('app.name', 'Your Company'),
                'tagline' => 'Premium Services',
                'address' => '123 Business St, City, State 12345',
                'email' => 'info@company.com',
                'phone' => '+1 (555) 123-4567',
            ]
        ];

        // Generate PDF
        $pdf = Pdf::loadView('order.invoice', $data);


        // Generate filename
        $filename = "invoice-{$order->order_number}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Export orders (for admin/vendor)
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        $query = Order::with(['vendor', 'customer']);

        if ($user->isVendor()) {
            $query->where('vendor_id', $user->vendor->id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Export logic here (CSV, Excel, etc.)
        // You can use Laravel Excel or create a simple CSV download

        return view('orders.export', compact('orders'));
    }

    /**
     * Get order statistics
     */
    public function stats()
    {
        $user = Auth::user();

        $query = Order::query();

        if ($user->isVendor()) {
            $query->where('vendor_id', $user->vendor->id);
        }

        $stats = [
            'total_orders' => $query->count(),
            'pending_orders' => (clone $query)->where('status', 'pending')->count(),
            'processing_orders' => (clone $query)->where('status', 'processing')->count(),
            'completed_orders' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_orders' => (clone $query)->where('status', 'cancelled')->count(),
            'total_revenue' => (clone $query)->where('payment_status', 'paid')->sum('total_amount'),
            'today_orders' => (clone $query)->whereDate('created_at', Carbon::today())->count(),
            'today_revenue' => (clone $query)->whereDate('created_at', Carbon::today())
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'this_month_orders' => (clone $query)->whereMonth('created_at', Carbon::now()->month)->count(),
            'this_month_revenue' => (clone $query)->whereMonth('created_at', Carbon::now()->month)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];

        return view('orders.stats', compact('stats'));
    }

    /**
     * Bulk update order status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $orders = Order::whereIn('id', $request->order_ids);

            if ($user->isVendor()) {
                $orders->where('vendor_id', $user->vendor->id);
            }

            $orders->update(['status' => $request->status]);

            DB::commit();

            return back()->with('success', count($request->order_ids) . ' orders updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update orders: ' . $e->getMessage());
        }
    }
}
