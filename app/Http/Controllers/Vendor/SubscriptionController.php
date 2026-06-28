<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\PaymentMethod;
use Stripe\Subscription as StripeSubscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        //DB::table('migrations')->where('id', 5)->delete();

        $vendor = Auth::user()->vendor;
        // $vendor->stripe_account_id = 'acct_1TiedPAtlaAtIJ4A';
        // $vendor->stripe_account_status = 'active';
        // $vendor->save();

        // return $vendor;

        // Get all available plans
        $plans = Plan::active()->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => $plan->price,
                'type' => $plan->type,
                'billing_cycle' => $plan->billing_cycle ?? 'monthly',
                'features' => $plan->features ?? [],
                'status' => $plan->status,
            ];
        });

        // Get current subscription
        $currentSubscription = null;
        $trialMode = false;
        $trialDaysLeft = 0;

        $invoice = Invoice::where('vendor_id', $vendor->id)
            //->where('type', 'one_time')
            ->where('status', 'paid')
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($invoice) {
            $currentSubscription = [
                'id' => $invoice->id,
                'plan_id' => $invoice->plan_id,
                'plan_name' => $invoice->plan->name ?? 'Unknown Plan',
                'type' => $invoice->type,
                'amount' => $invoice->amount,
                'status' => $vendor->subscription_status,
                'started_at' => $invoice->created_at,
                'next_billing_date' => $invoice->billing_period_ends,
                'total_paid' => $invoice->amount,
            ];
        } else {
            // Check if vendor has a trial
            if ($vendor->trial_ends_at && Carbon::now()->lt($vendor->trial_ends_at)) {
                $trialMode = true;
                $trialDaysLeft = Carbon::now()->diffInDays($vendor->trial_ends_at);
            }
        }

        // Get invoices
        $invoices = Invoice::where('vendor_id', $vendor->id)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number ?? 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
                    'plan_name' => $invoice->plan->name ?? 'Unknown Plan',
                    'type' => $invoice->type,
                    'amount' => $invoice->amount,
                    'status' => $invoice->status,
                    'created_at' => $invoice->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'vendor' => $vendor,
                'current_subscription' => $currentSubscription,
                'trial_mode' => $trialMode,
                'trial_days_left' => $trialDaysLeft,
                'plans' => $plans,
                'invoices' => $invoices,
                'stripe_key' => Config('services.stripe.key')
            ]
        ]);
    }

    public function purchaseOneTime(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:cash,stripe'
        ]);

        $vendor = Auth::user()->vendor;
        $plan = Plan::findOrFail($request->plan_id);

        if ($plan->type !== 'one_time') {
            return response()->json([
                'success' => false,
                'message' => 'This plan is not a one-time invoice'
            ], 400);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Update vendor plan
            $vendor->plan_id = $request->plan_id;
            $vendor->save();

            // Generate invoice number
            $invoiceNumber = 'INV-' . str_pad(Invoice::max('id') + 1, 6, '0', STR_PAD_LEFT);

            // Handle Stripe payment
            if ($request->payment_method === 'stripe') {
                // Validate stripe payment method ID
                if (!$request->has('payment_method_id')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment method ID is required for Stripe payments'
                    ], 400);
                }

                // Process Stripe payment
                $paymentResult = $this->processStripePayment($vendor, $plan, $request->payment_method_id);

                if (!$paymentResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $paymentResult['message']
                    ], 400);
                }

                $status = 'paid';
                $stripePaymentIntentId = $paymentResult['payment_intent_id'];
                $paidAt = Carbon::now();
            } else {
                // Cash payment
                $status = 'pending';
                $stripePaymentIntentId = null;
                $paidAt = null;
            }

            // Create invoice
            $invoice = Invoice::create([
                'vendor_id' => $vendor->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'type' => 'one_time',
                'payment_method' => $request->payment_method,
                'status' => $status,
                'invoice_number' => $invoiceNumber,
                'paid_at' => $paidAt,
            ]);

            if ($status === 'paid') {
                $vendor->subscription_status = 'active';
                $vendor->subscription_ends_at = null;
                $vendor->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $status === 'paid'
                    ? 'Payment successful! Plan activated.'
                    : 'Invoice created successfully. Our team will contact you for payment.',
                'data' => [
                    'invoice' => $invoice,
                    'status' => $status,
                    'payment_intent_id' => $stripePaymentIntentId,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processStripePayment($vendor, $plan, $paymentMethodId)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create or get Stripe customer
            if (!$vendor->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $vendor->user->email,
                    'name' => $vendor->business_name,
                    'metadata' => [
                        'vendor_id' => $vendor->id,
                        'vendor_name' => $vendor->business_name,
                    ]
                ]);
                $vendor->stripe_customer_id = $customer->id;
                $vendor->save();
            }

            // Attach payment method to customer
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

            $paymentMethod->attach(['customer' => $vendor->stripe_customer_id]);

            \Stripe\Customer::update($vendor->stripe_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethod->id
                ]
            ]);

            // Create payment intent
            $amount = $plan->price * 100; // Convert to cents
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'customer' => $vendor->stripe_customer_id,
                'payment_method' => $paymentMethod->id,
                'off_session' => false,
                'confirm' => true, // CONFIRM IMMEDIATELY
                'capture_method' => 'automatic', // Capture payment immediately
                'return_url' => url('/vendor/subscription?payment=success'),
                'metadata' => [
                    'vendor_id' => $vendor->id,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'invoice_type' => 'one_time',
                    'vendor_email' => $vendor->user->email,
                ],
                'description' => "One-time payment for {$plan->name} plan - Vendor: {$vendor->business_name}",
                'receipt_email' => $vendor->user->email,
            ]);

            // Check payment status
            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'payment_intent_id' => $paymentIntent->id,
                    'message' => 'Payment successful'
                ];
            } elseif ($paymentIntent->status === 'requires_action') {
                return [
                    'success' => true,
                    'requires_action' => true,
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'message' => 'Additional authentication required'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment failed: ' . $paymentIntent->status
                ];
            }
        } catch (\Stripe\Exception\CardException $e) {
            return [
                'success' => false,
                'message' => 'Card error: ' . $e->getError()->message
            ];
        } catch (\Stripe\Exception\RateLimitException $e) {
            return [
                'success' => false,
                'message' => 'Too many requests. Please try again later.'
            ];
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return [
                'success' => false,
                'message' => 'Invalid request: ' . $e->getMessage()
            ];
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return [
                'success' => false,
                'message' => 'Stripe authentication failed. Please contact support.'
            ];
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            return [
                'success' => false,
                'message' => 'Network error. Please try again.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            return response()->json([
                'success' => true,
                'status' => $paymentIntent->status,
                'data' => $paymentIntent
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function createSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method_id' => 'required|string',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        if ($plan->type !== 'subscription') {
            return response()->json([
                'success' => false,
                'message' => 'This plan is not a subscription',
            ], 400);
        }

        $vendor = Auth::user()->vendor;

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Update vendor plan
            $vendor->plan_id = $plan->id;
            $vendor->save();

            // Create Stripe customer if needed
            if (!$vendor->stripe_customer_id) {

                $customer = \Stripe\Customer::create([
                    'email' => Auth::user()->email,
                    'name' => $vendor->business_name ?? Auth::user()->name,
                    'metadata' => [
                        'vendor_id' => $vendor->id,
                    ],
                ]);

                $vendor->stripe_customer_id = $customer->id;
                $vendor->save();
            }

            // Attach payment method
            $paymentMethod = PaymentMethod::retrieve(
                $request->payment_method_id
            );

            $paymentMethod->attach([
                'customer' => $vendor->stripe_customer_id,
            ]);

            \Stripe\Customer::update(
                $vendor->stripe_customer_id,
                [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethod->id,
                    ],
                ]
            );

            // Create subscription
            $stripeSubscription = StripeSubscription::create([
                'customer' => $vendor->stripe_customer_id,
                'items' => [
                    [
                        'price' => $plan->price_id,
                    ]
                ],
                'default_payment_method' => $paymentMethod->id,

                'expand' => [
                    'latest_invoice.payment_intent'
                ]
            ]);

            if ($stripeSubscription->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed',
                    'status' => $stripeSubscription->status
                ], 400);
            }

            $subscriptionItem = $stripeSubscription->items->data[0] ?? null;
            $startsDate = Carbon::createFromTimestamp($subscriptionItem->current_period_start);
            $endsDate = Carbon::createFromTimestamp($subscriptionItem->current_period_end);


            // Save subscription details
            $vendor->stripe_subscription_id = $stripeSubscription->id;
            $vendor->subscription_status = $stripeSubscription->status;
            $vendor->subscription_starts_at = $startsDate;
            $vendor->subscription_ends_at = $endsDate;


            $invoice = new Invoice();
            $invoice->invoice_number =  $this->generateInvoiceNumber();
            $invoice->vendor_id =  $vendor->id;
            $invoice->plan_id =  $plan->id;
            $invoice->amount =  $plan->price;
            $invoice->total =  $plan->price;
            $invoice->type =  'subscription';
            $invoice->payment_method =  $request->payment_method;
            $invoice->billing_period_starts = $startsDate;
            $invoice->billing_period_ends = $endsDate;
            $invoice->stripe_invoice_id = $stripeSubscription->latest_invoice->id;
            $invoice->status = 'paid';
            $invoice->paid_at = now();
            $invoice->save();

            $vendor->save();

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully',
                'subscription_id' => $stripeSubscription->id,
            ]);
        } catch (\Exception $e) {

            Log::error('Subscription creation failed', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription',
            ], 500);
        }
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1;

        return $prefix . '-' . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function cancel(Request $request)
    {
        $vendor = Auth::user()->vendor;
        if ($vendor->stripe_subscription_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor is not subscribed'
            ], 500);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $stripeSubscription = StripeSubscription::retrieve($vendor->stripe_subscription_id);
            $stripeSubscription->cancel();

            $vendor->subscription_status = 'cancelled';
            $vendor->updated_at = Carbon::now();
            $vendor->save();

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadInvoice(Request $request, $invoiceId)
    {

        $invoice = Invoice::where('id', $invoiceId)
            ->with(['plan', 'vendor'])
            ->first();

        $vendor = Auth::user()->vendor;
        //return $invoice;
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        $settings = Settings::where('is_active', true)->get();
        $company_name = $settings->where('key', 'name')->value('value');
        $company_address = $settings->where('key', 'address')->value('value');
        $company_email = $settings->where('key', 'email')->value('value');
        $company_phone = $settings->where('key', 'phone')->value('value');
        $company_logo = $settings->where('key', 'logo')->value('value');

        $logoPath = null;
        if ($company_logo) {
            $logoPath = public_path('storage/company-logos/' . $company_logo);
        }

        try {
            // Prepare invoice data
            $data = [
                'invoice' => $invoice,
                'vendor' => $vendor,
                'plan' => $invoice->plan,
                'company_name' => $company_name ?? config('app.name', 'OrderValut'),
                'company_address' => $company_address ?? config('app.address', '123 Business Street, City, Country'),
                'company_email' => $company_email ?? config('app.email', 'support@example.com'),
                'company_phone' => $company_phone ?? config('app.phone', '+1 234 567 890'),
                'company_logo' => $company_logo,
                'logoPath' => $logoPath,
                'generated_at' => Carbon::now(),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('plan.invoice', $data);

            // Set PDF options
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            // Generate filename
            $filename = "invoice-{$invoice->invoice_number}.pdf";

            // Return PDF download
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Invoice download failed', [
                'invoice_id' => $invoiceId,
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createStripeAccount(Request $request)
    {
        try {
            $user = Auth::user();
            $vendor = $user->vendor;

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found'
                ], 404);
            }

            if ($vendor->stripe_account_id) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor already has a Stripe account',
                    'data' => ['stripe_account_id' => $vendor->stripe_account_id]
                ]);
            }

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));



            $account = $stripe->v2->core->accounts->create([
                'contact_email' => $user->email,
                'display_name' => $vendor->business_name,

                'identity' => [
                    'country' => 'US',
                    'entity_type' => 'company',
                    'business_details' => [
                        'registered_name' => $vendor->business_name,
                    ],
                ],

                'configuration' => [
                    'merchant' => [
                        'capabilities' => [
                            'card_payments' => [
                                'requested' => true,
                            ],
                        ],
                    ],
                ],

                'defaults' => [
                    'responsibilities' => [
                        'fees_collector' => 'stripe',
                        'losses_collector' => 'stripe',
                    ],
                ],

                'dashboard' => 'full',
            ]);

            $accountLink = $stripe->accountLinks->create([
                'account' => $account->id,
                'refresh_url' => route('stripe.refresh'),
                'return_url' => route('stripe.success', [
                    'vendor_id' => $vendor->id
                ]),
                'type' => 'account_onboarding',
            ]);

            $vendor->update([
                'stripe_account_id' => $account->id,
                'stripe_account_status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stripe account created successfully',
                'data' => [
                    'stripe_account_id' => $account->id,
                    'onboarding_url' => $accountLink->url,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Create Stripe account error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Stripe account: ' . $e->getMessage()
            ], 500);
        }
    }

    public function refreshStripeOnboarding(Request $request)
    {
        try {
            $vendor = Auth::user()->vendor;

            if (!$vendor || !$vendor->stripe_account_id) {
                return redirect()->route('vendor.stripe.onboarding')
                    ->with('error', 'Invalid vendor or no Stripe account found');
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            $accountLink = AccountLink::create([
                'account' => $vendor->stripe_account_id,
                'refresh_url' => route('stripe.refresh', ['vendor_id' => $vendor->id]),
                'return_url' => route('stripe.return', ['vendor_id' => $vendor->id]),
                'type' => 'account_onboarding',
            ]);

            return redirect()->away($accountLink->url);
        } catch (\Exception $e) {
            Log::error('Refresh Stripe onboarding error: ' . $e->getMessage());
            return redirect()->route('vendor.stripe.onboarding')
                ->with('error', 'Failed to refresh onboarding: ' . $e->getMessage());
        }
    }

    public function handleStripeReturn(Request $request)
    {
        try {
            $vendor = Auth::user()->vendor;

            if (!$vendor || !$vendor->stripe_account_id) {
                return redirect()->route('vendor.stripe.onboarding')
                    ->with('error', 'Invalid vendor');
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            // Check account status
            $account = Account::retrieve($vendor->stripe_account_id);

            // Check if onboarding is complete
            if ($account->charges_enabled && $account->payouts_enabled) {
                return redirect()->route('vendor.stripe.onboarding')
                    ->with('success', 'Stripe account successfully connected!');
            } else {
                return redirect()->route('vendor.stripe.onboarding')
                    ->with('error', 'Onboarding incomplete. Please complete all required steps.');
            }
        } catch (\Exception $e) {
            Log::error('Handle Stripe return error: ' . $e->getMessage());
            return redirect()->route('vendor.stripe.onboarding')
                ->with('error', 'Failed to complete onboarding: ' . $e->getMessage());
        }
    }

    public function handleStripeSuccess(Request $request)
    {
        try {
            $vendor = Auth::user()->vendor;

            if (!$vendor) {
                \Log::error('Vendor not found for Stripe success callback', [
                    'user_id' => Auth::id()
                ]);
                return redirect()->route('vendor.stripe.onboarding')
                    ->with('error', 'Vendor profile not found. Please contact support.');
            }

            if (!$vendor->stripe_account_id) {
                \Log::error('No Stripe account ID found for vendor', [
                    'vendor_id' => $vendor->id,
                    'user_id' => Auth::id()
                ]);
                return redirect()->route('vendor.stripe.onboarding')
                    ->with('error', 'Stripe account not found. Please start the onboarding process again.');
            }

            \Log::info('Checking Stripe account status', [
                'vendor_id' => $vendor->id,
                'stripe_account_id' => $vendor->stripe_account_id
            ]);

            Stripe::setApiKey(config('services.stripe.secret'));

            // Check account status
            $account = Account::retrieve($vendor->stripe_account_id);

            // Check if onboarding is complete
            if ($account->charges_enabled && $account->payouts_enabled) {
                \Log::info('Stripe account successfully connected', [
                    'vendor_id' => $vendor->id,
                    'stripe_account_id' => $vendor->stripe_account_id,
                ]);

                return redirect()->route('vendor.stripe.onboarding')
                    ->with('success', 'Stripe account successfully connected! You can now receive payments.');
            } else {
                $requirements = $account->requirements ?? null;
                $currentlyDue = $requirements->currently_due ?? [];

                \Log::warning('Stripe onboarding incomplete', [
                    'vendor_id' => $vendor->id,
                    'stripe_account_id' => $vendor->stripe_account_id,
                    'charges_enabled' => $account->charges_enabled,
                    'payouts_enabled' => $account->payouts_enabled,
                    'currently_due' => $currentlyDue
                ]);

                $errorMessage = 'Onboarding incomplete.';
                if (!empty($currentlyDue)) {
                    $errorMessage .= ' Please provide the following information: ' . implode(', ', $currentlyDue);
                } else {
                    $errorMessage .= ' Please complete all required steps.';
                }

                return redirect()->route('vendor.stripe.onboarding')
                    ->with('error', $errorMessage);
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API error in handleStripeSuccess', [
                'vendor_id' => $vendor->id ?? null,
                'error' => $e->getMessage(),
                'error_type' => get_class($e)
            ]);

            return redirect()->route('vendor.stripe.onboarding')
                ->with('error', 'Stripe API error: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Unexpected error in handleStripeSuccess', [
                'vendor_id' => $vendor->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('vendor.stripe.onboarding')
                ->with('error', 'Failed to complete onboarding: ' . $e->getMessage());
        }
    }


    public function status()
    {
        $user = Auth::user();

        if (!$user || !$user->vendor) {
            return response()->json([
                'success' => false,
                'error' => 'Vendor not found'
            ], 404);
        }

        $vendor = $user->vendor;
        $isValid = $this->checkSubscriptionValidity($vendor);

        $invoice = Invoice::where('vendor_id', $vendor->id)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'is_valid' => $isValid['valid'],
                'status' => $vendor->subscription_status ?? 'inactive',
                'message' => $isValid['message'],
                'plan_name' => $invoice->plan->name ?? null,
                'plan_type' => $invoice->type ?? null,
                'trial_ends_at' => $vendor->trial_ends_at,
                'subscription_ends_at' => $vendor->subscription_ends_at,
                'has_active_subscription' => $isValid['valid'],
                'requires_subscription' => true,
                'redirect_url' => '/vendor/subscription'
            ]
        ]);
    }

    private function checkSubscriptionValidity($vendor): array
    {
        $status = $vendor->subscription_status ?? 'inactive';

        if ($status === 'active') {
            if ($vendor->subscription_ends_at && now()->greaterThan($vendor->subscription_ends_at)) {
                return [
                    'valid' => false,
                    'message' => 'Your subscription has expired. Please renew to continue.'
                ];
            }
            return ['valid' => true, 'message' => 'Active subscription'];
        }

        // If status is trial
        if ($status === 'trial') {
            if ($vendor->trial_ends_at && now()->greaterThan($vendor->trial_ends_at)) {
                return [
                    'valid' => false,
                    'message' => 'Your free trial has expired. Please subscribe to continue.'
                ];
            }
            return ['valid' => true, 'message' => 'Trial active'];
        }

        // If status is pending
        if ($status === 'pending') {
            return [
                'valid' => false,
                'message' => 'Your vendor account is pending approval.'
            ];
        }

        // Default - no subscription
        return [
            'valid' => false,
            'message' => 'No active subscription found. Please purchase a plan to continue.'
        ];
    }
}
