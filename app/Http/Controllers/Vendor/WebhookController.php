<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Carbon\Carbon;

class WebhookController extends Controller
{

    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 400);
        }

        Log::info('Stripe webhook received', [
            'event' => $event->type,
            'data' => $event->data->object
        ]);

        // Handle the event
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event);
                break;

            case 'customer.subscription.trial_will_end':
                $this->handleTrialWillEnd($event);
                break;

            default:
                Log::info('Unhandled Stripe event type: ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }



    protected function handleInvoicePaymentSucceeded(Event $event)
    {
        $invoice = $event->data->object;

        $subscription = Vendor::where('stripe_subscription_id', $invoice->subscription)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for invoice payment', [
                'stripe_subscription_id' => $invoice->subscription
            ]);
            return;
        }

        $currentEndDate = $subscription->subscription_ends_at;
        $now = Carbon::now();

        $periodEnd = Carbon::createFromTimestamp($invoice->period_end ?? $invoice->created + 30 * 24 * 60 * 60);
        $periodStart = Carbon::createFromTimestamp($invoice->period_start ?? $invoice->created);

        if ($currentEndDate && $currentEndDate->isFuture()) {
            $newEndDate = $currentEndDate->copy()->addDays($periodStart->diffInDays($periodEnd));
        } else {
            $newEndDate = $now->copy()->addDays($periodStart->diffInDays($periodEnd));
        }

        $subscription->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => $newEndDate,
        ]);


        // Log success
        Log::info('Invoice payment succeeded', [
            'subscription_id' => $subscription->id,
            'vendor_id' => $subscription->vendor_id,
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount_paid,
            'new_end_date' => $newEndDate->toDateTimeString()
        ]);
    }

    protected function recordInvoice($subscription, $invoice)
    {
        try {
            $plan = $subscription->plan;
            $vendor = $subscription->vendor;

            $amount = $invoice->amount_paid / 100;
            $currency = strtoupper($invoice->currency);

            $invoiceData = [
                'vendor_id' => $vendor->id,
                'plan_id' => $plan->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'stripe_invoice_id' => $invoice->id,
                'amount' => $amount,
                'subtotal' => $amount,
                'tax' => $invoice->tax ?? 0,
                'discount' => $invoice->discount ?? 0,
                'total' => $amount,
                'currency' => $currency,
                'status' => 'paid',
                'due_date' => Carbon::now(),
                'paid_at' => Carbon::now(),
                'type' => 'subscription',
                'billing_period_start' => Carbon::createFromTimestamp($invoice->period_start ?? $invoice->created),
                'billing_period_end' => Carbon::createFromTimestamp($invoice->period_end ?? $invoice->created + 30 * 24 * 60 * 60),
                'notes' => 'Payment for ' . $plan->name . ' plan subscription',
                'items' => json_encode([
                    [
                        'subscription_id' => $subscription->id,
                        'name' => $plan->name . ' Plan - ' . ucfirst($plan->billing_period ?? 'monthly'),
                        'description' => $plan->description ?? 'Subscription plan',
                        'quantity' => 1,
                        'unit_price' => $amount,
                        'total' => $amount,
                    ]
                ])
            ];

            // Create invoice record
            $createdInvoice = Invoice::create($invoiceData);

            // Log the invoice creation
            Log::info('Invoice recorded successfully', [
                'invoice_number' => $createdInvoice->invoice_number,
                'vendor_id' => $vendor->id,
                'amount' => $amount,
                'stripe_invoice_id' => $invoice->id
            ]);

            return $createdInvoice;
        } catch (\Exception $e) {
            Log::error('Failed to record invoice', [
                'error' => $e->getMessage(),
                'stripe_invoice_id' => $invoice->id,
                'subscription_id' => $subscription->id
            ]);

            return null;
        }
    }


    protected function handleInvoicePaymentFailed(Event $event)
    {
        $invoice = $event->data->object;

        $subscription = Vendor::where('stripe_subscription_id', $invoice->subscription)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for invoice payment failed', [
                'stripe_subscription_id' => $invoice->subscription
            ]);
            return;
        }

        $subscription->update([
            'subscription_status' => 'past_due',
        ]);


        $this->sendPaymentFailedNotification($subscription->vendor, $invoice);

        Log::warning('Invoice payment failed', [
            'subscription_id' => $subscription->id,
            'invoice_id' => $invoice->id,
            'attempt' => $subscription->payment_attempts
        ]);
    }


    protected function handleTrialWillEnd(Event $event)
    {
        $stripeSubscription = $event->data->object;

        $subscription = Vendor::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for trial end', [
                'stripe_subscription_id' => $stripeSubscription->id
            ]);
            return;
        }

        // Notify vendor about trial ending
        $this->sendTrialEndingNotification($subscription->vendor, $stripeSubscription->trial_end);

        Log::info('Trial will end soon', [
            'subscription_id' => $subscription->id,
            'vendor_id' => $subscription->vendor_id,
            'trial_end_date' => Carbon::createFromTimestamp($stripeSubscription->trial_end)
        ]);
    }


    protected function sendPaymentFailedNotification($vendor, $invoice)
    {
        Log::info('Payment failed notification sent', [
            'vendor_id' => $vendor->id,
            'invoice_id' => $invoice->id
        ]);
    }


    protected function sendTrialEndingNotification($vendor, $trialEndTimestamp)
    {
        Log::info('Trial ending notification sent', [
            'vendor_id' => $vendor->id,
            'trial_end_date' => Carbon::createFromTimestamp($trialEndTimestamp)
        ]);
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
}
