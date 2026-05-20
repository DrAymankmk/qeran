<?php

namespace App\Services\Auth;

use App\Helpers\Constant;
use App\Models\VerificationCode;
use App\Services\Auth\Exceptions\VerificationOtpDeliveryException;
use App\Services\External\BaileysGateway;
use App\Services\External\BaileysWhatsApp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationMail;

class VerificationService
{
    /**
     * Generate and deliver a verification code (phone via WhatsApp system session).
     *
     * @throws VerificationOtpDeliveryException
     */
    public static function verifyAccount(
        $user_id,
        $objective,
        $information_type,
        $information,
        $country_code
    ): void {
        if (empty($user_id)) {
            Log::warning('OTP: missing user_id', [
                'objective' => $objective,
                'information_type' => $information_type,
            ]);

            throw new VerificationOtpDeliveryException(
                'user_not_found',
                __('messages.otp_user_not_found'),
                ['objective' => $objective]
            );
        }

        $activation_code = (string) rand(1000, 9999);

        if ($information_type == Constant::VERIFICATION_INFORMATION_TYPE['Phone']) {
            self::deliverPhoneOtp(
                (int) $user_id,
                (string) $country_code,
                (string) $information,
                $activation_code,
                (int) $objective
            );
        } else {
            self::deliverEmailOtp((string) $information, $activation_code, (int) $user_id, (int) $objective);
        }

        VerificationCode::updateOrInsert(
            [
                'user_id' => $user_id,
                'objective' => $objective,
                'information_type' => $information_type,
                'information' => $information,
                'country_code' => $country_code,
            ],
            [
                'code' => $activation_code,
                'used' => Constant::VERIFICATION_USED['Not used'],
                'expired_at' => now()->addHour()->toDateTimeString(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * @throws VerificationOtpDeliveryException
     */
    protected static function deliverPhoneOtp(
        int $userId,
        string $countryCode,
        string $phone,
        string $activationCode,
        int $objective
    ): void {
        $context = [
            'user_id' => $userId,
            'objective' => $objective,
            'country_code' => $countryCode,
            'phone_masked' => self::maskPhone($countryCode, $phone),
        ];

        if (! BaileysGateway::isConfigured()) {
            Log::error('OTP WhatsApp: gateway not configured in Laravel .env', $context);

            throw new VerificationOtpDeliveryException(
                'gateway_not_configured',
                __('messages.otp_gateway_not_configured'),
                $context
            );
        }

        $to = BaileysGateway::normalizeUserPhone($countryCode, $phone);

        if ($to === '') {
            Log::warning('OTP WhatsApp: invalid phone number', $context);

            throw new VerificationOtpDeliveryException(
                'invalid_phone',
                __('messages.otp_invalid_phone'),
                $context
            );
        }

        $context['to'] = self::maskDigits($to);

        $sessionId = BaileysGateway::systemSessionId();
        $statusResult = BaileysGateway::getStatus($sessionId);

        if (! $statusResult['ok']) {
            Log::error('OTP WhatsApp: could not reach gateway status endpoint', array_merge($context, [
                'gateway_error' => $statusResult['error'] ?? null,
                'http_status' => $statusResult['status'] ?? 0,
            ]));

            throw new VerificationOtpDeliveryException(
                'gateway_unreachable',
                __('messages.otp_gateway_unreachable'),
                $context
            );
        }

        $connectionStatus = $statusResult['data']['status'] ?? 'disconnected';

        if ($connectionStatus !== 'connected') {
            Log::warning('OTP WhatsApp: system session not connected', array_merge($context, [
                'session_id' => $sessionId,
                'gateway_status' => $connectionStatus,
                'hint' => 'Link qeran system number in admin → ربط واتساب (OTP)',
            ]));

            throw new VerificationOtpDeliveryException(
                'system_not_connected',
                __('messages.otp_system_whatsapp_not_connected'),
                array_merge($context, ['gateway_status' => $connectionStatus])
            );
        }

        $response = BaileysWhatsApp::sendLegacy($to, $activationCode);

        if (! isset($response->sent) || $response->sent !== 'true') {
            $errorMessage = is_object($response->error ?? null)
                ? ($response->error->message ?? 'unknown')
                : 'unknown';

            Log::error('OTP WhatsApp: send failed', array_merge($context, [
                'session_id' => $sessionId,
                'gateway_error' => $errorMessage,
                'gateway_http_status' => $response->error->status ?? null,
            ]));

            throw new VerificationOtpDeliveryException(
                'send_failed',
                __('messages.otp_whatsapp_send_failed'),
                array_merge($context, ['gateway_error' => $errorMessage])
            );
        }

        Log::info('OTP WhatsApp: sent successfully', array_merge($context, [
            'session_id' => $sessionId,
            'message_id' => $response->id ?? null,
        ]));
    }

    /**
     * @throws VerificationOtpDeliveryException
     */
    protected static function deliverEmailOtp(
        string $email,
        string $activationCode,
        int $userId,
        int $objective
    ): void {
        $context = [
            'user_id' => $userId,
            'objective' => $objective,
            'email' => $email,
        ];

        try {
            Mail::to($email)->send(new ActivationMail(['code' => $activationCode]));
            Log::info('OTP email: sent successfully', $context);
        } catch (\Throwable $e) {
            Log::error('OTP email: send failed', array_merge($context, [
                'error' => $e->getMessage(),
            ]));

            throw new VerificationOtpDeliveryException(
                'email_send_failed',
                __('messages.otp_email_send_failed'),
                $context,
                0,
                $e
            );
        }
    }

    protected static function maskPhone(string $countryCode, string $phone): string
    {
        return self::maskDigits(BaileysGateway::normalizeUserPhone($countryCode, $phone));
    }

    protected static function maskDigits(string $digits): string
    {
        if (strlen($digits) <= 4) {
            return '****';
        }

        return str_repeat('*', strlen($digits) - 4).substr($digits, -4);
    }
}
