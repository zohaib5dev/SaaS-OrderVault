<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $query = User::where('role', 'customer');

        // Search by name, email, or phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status (active/inactive based on email verification)
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNull('email_verified_at');
            }
        }

        // Sorting
        $sort = $request->get('sort', 'name');
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'orders':
                $query->orderBy('orders_count', 'desc');
                break;
            case 'name':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $customers = $query->paginate($perPage);

        // Transform the data to include total_spent
        $customers->getCollection()->transform(function ($customer) use ($vendor) {
            // Calculate total spent for this vendor
            $ordersCount = Order::where('vendor_id', $vendor->id)
                ->where('customer_id', $customer->id)
                ->where('payment_status', 'paid')
                ->count();

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'status' => $customer->email_verified_at ? 'active' : 'inactive',
                'orders_count' => $ordersCount ?? 0,
                'created_at' => $customer->created_at,
                'updated_at' => $customer->updated_at,
            ];
        });

        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'active_customers' => User::where('role', 'customer')->where('status', 'active')->count(),
            'inactive_customers' => User::where('role', 'customer')->where('status', 'inactive')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $customers,
            'stats' => $stats,
        ]);
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'role' => 'customer',
                'password' => Hash::make($request->password),
                'email_verified_at' => $request->is_active ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = User::with(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $vendor = Auth::user()->vendor;

        $orders = Order::where('vendor_id', $vendor->id)->where('customer_id', $customer->id)->orderBy('created_at');
        $totalSpent = $orders->sum('total_amount');
        $ordersCount = $orders->count();

        $lastOrder = null;
        if ($orders->count() > 0)
            $lastOrder = $orders->first();

        $orders = $orders->get();

        // Format customer data
        $customerData = [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'status' => $customer->email_verified_at ? 'active' : 'inactive',
            'orders_count' => $ordersCount ?? 0,
            'total_spent' => $totalSpent ?? 0,
            'created_at' => $customer->created_at,
            'last_order_date' => $lastOrder ? $lastOrder->created_at : null,
            'orders' => $orders,
            'updated_at' => $customer->updated_at,
            'is_active' => $customer->email_verified_at ? true : false,
        ];

        return response()->json([
            'success' => true,
            'data' => $customerData
        ]);
    }
}
