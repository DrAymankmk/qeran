<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $provider = 'default')
    {
        if (!$this->verifySignature($request, $provider)) {
            Log::warning('Webhook signature verification failed', [
                'provider' => $provider,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'headers' => $request->headers->all()
            ]);

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    /**
     * Verify the webhook signature based on provider
     */
    private function verifySignature(Request $request, string $provider): bool
    {
        switch ($provider) {
            case 'whatsapp':
                return $this->verifyWhatsAppSignature($request);
            case 'stripe':
                return $this->verifyStripeSignature($request);
            case 'paypal':
                return $this->verifyPayPalSignature($request);
            default:
                return $this->verifyDefaultSignature($request);
        }
    }

    /**
     * Verify WhatsApp webhook signature
     */
    private function verifyWhatsAppSignature(Request $request): bool
    {
        // For UltraMessage and similar services
        $allowedIPs = [
            '185.37.37.37', // UltraMessage IP - update with actual IPs
            '127.0.0.1', // Local testing
        ];

        // Check IP whitelist
        if (in_array($request->ip(), $allowedIPs)) {
            return true;
        }

        // If you have a webhook token/secret from your provider
        $webhookToken = config('services.whatsapp.webhook_token');
        $providedToken = $request->header('X-Webhook-Token') ?? $request->input('token');

        if ($webhookToken && $providedToken === $webhookToken) {
            return true;
        }

        // Allow in local environment for testing
        return app()->environment('local');
    }

    /**
     * Verify Stripe webhook signature
     */
    private function verifyStripeSignature(Request $request): bool
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (!$signature || !$secret) {
            return false;
        }

        try {
            \Stripe\Webhook::constructEvent($payload, $signature, $secret);
            return true;
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verify PayPal webhook signature
     */
    private function verifyPayPalSignature(Request $request): bool
    {
        // Implement PayPal webhook verification
        // This is a basic implementation - enhance based on PayPal's requirements
        $webhookId = config('services.paypal.webhook_id');
        $providedId = $request->header('PAYPAL-TRANSMISSION-ID');

        return $webhookId && $providedId && $webhookId === $providedId;
    }

    /**
     * Default signature verification
     */
    private function verifyDefaultSignature(Request $request): bool
    {
        // Basic implementation for generic webhooks
        $secret = config('app.webhook_secret', 'default_secret');
        $signature = $request->header('X-Signature');
        $payload = $request->getContent();

        if (!$signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($signature, $expectedSignature);
    }
} 
