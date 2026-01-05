<?php

namespace App\Services\External;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioWhatsApp
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        $this->from = trim(config('services.twilio.whatsapp_from', '')); // Trim whitespace
        $messagingServiceSid = config('services.twilio.whatsapp_messaging_service_sid');

        if (empty($accountSid)) {
            Log::error('Twilio Account SID is not configured. Please set TWILIO_ACCOUNT_SID in your .env file.');
            throw new \Exception('Twilio Account SID is not configured. Please set TWILIO_ACCOUNT_SID in your .env file.');
        }

        if (empty($authToken)) {
            Log::error('Twilio Auth Token is not configured. Please set TWILIO_AUTH_TOKEN in your .env file.');
            throw new \Exception('Twilio Auth Token is not configured. Please set TWILIO_AUTH_TOKEN in your .env file.');
        }

        // Use Messaging Service SID if available, otherwise use from number
        if (!empty($messagingServiceSid)) {
            $this->from = $messagingServiceSid;
        } elseif (empty($this->from)) {
            Log::error('Twilio WhatsApp From number is not configured. Please set TWILIO_WHATSAPP_FROM or TWILIO_WHATSAPP_MESSAGING_SERVICE_SID in your .env file.');
            throw new \Exception('Twilio WhatsApp From number is not configured. Please set TWILIO_WHATSAPP_FROM or TWILIO_WHATSAPP_MESSAGING_SERVICE_SID in your .env file.');
        }

        $this->client = new Client($accountSid, $authToken);
    }

    /**
     * Send WhatsApp message via Twilio
     * 
     * @param string $to Phone number in format: +1234567890 or whatsapp:+1234567890
     * @param string $message Message body
     * @param string $referenceId Optional reference ID for tracking
     * @return object Response object
     */
    public static function send($to, $message, $referenceId = '')
    {
        try {
            $instance = new self();
            
            // Ensure phone number is in correct format
            $to = self::formatPhoneNumber($to);
            
            // Prepare message options
            // If from starts with 'MG', it's a Messaging Service SID, otherwise it's a phone number
            $options = [
                'body' => $message,
            ];
            
            // Use 'messagingServiceSid' for Messaging Service, 'from' for phone number
            if (str_starts_with($instance->from, 'MG')) {
                $options['messagingServiceSid'] = $instance->from;
            } else {
                $options['from'] = $instance->from;
            }

            // Add status callback if referenceId is provided
            if (!empty($referenceId)) {
                $options['statusCallback'] = url('/api/webhooks/whatsapp/status');
            }

            // Log the from number being used for debugging
            Log::info('Twilio WhatsApp sending message', [
                'from' => $instance->from,
                'to' => $to,
                'has_reference_id' => !empty($referenceId)
            ]);

            // Send message
            $messageObj = $instance->client->messages->create($to, $options);

            // Return response in similar format to UltraMessage for compatibility
            return (object)[
                'sent' => $messageObj->status !== 'failed' ? 'true' : 'false',
                'id' => $messageObj->sid,
                'status' => $messageObj->status,
                'to' => $messageObj->to,
                'error' => $messageObj->errorCode ? (object)[
                    'code' => $messageObj->errorCode,
                    'message' => $messageObj->errorMessage
                ] : null
            ];

        } catch (\Twilio\Exceptions\RestException $e) {
            // Twilio-specific errors
            $errorMessage = $e->getMessage();
            
            // Provide user-friendly error messages
            if (str_contains($errorMessage, '401') || str_contains($errorMessage, 'Authenticate')) {
                $errorMessage = 'Twilio authentication failed. Please check your TWILIO_ACCOUNT_SID and TWILIO_AUTH_TOKEN in .env file.';
            } elseif ($e->getCode() == 63007 || str_contains($errorMessage, '63007') || str_contains($errorMessage, 'Channel with the specified From address')) {
                $fromNumber = config('services.twilio.whatsapp_from', 'not set');
                $messagingServiceSid = config('services.twilio.whatsapp_messaging_service_sid', 'not set');
                $errorMessage = "Twilio WhatsApp 'From' number is invalid or not enabled for WhatsApp. Current value: '{$fromNumber}'. ";
                $errorMessage .= "Please verify in Twilio Console that this number is enabled for WhatsApp. ";
                $errorMessage .= "Go to: Phone Numbers → Manage → Active numbers → Select your number → WhatsApp → Enable. ";
                $errorMessage .= "Alternatively, use a Messaging Service SID (starts with MG) by setting TWILIO_WHATSAPP_MESSAGING_SERVICE_SID in .env.";
            } elseif (str_contains($errorMessage, '21211')) {
                $errorMessage = 'Invalid phone number format. Phone number must be in E.164 format (e.g., +1234567890).';
            } elseif (str_contains($errorMessage, '21608')) {
                $errorMessage = 'WhatsApp number not registered. Please join the Twilio WhatsApp Sandbox or use an approved WhatsApp Business number.';
            }

            Log::error('Twilio WhatsApp send failed', [
                'to' => $to,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'status_code' => $e->getStatusCode(),
                'more_info' => $e->getMoreInfo(),
                'trace' => $e->getTraceAsString()
            ]);

            return (object)[
                'sent' => 'false',
                'error' => (object)[
                    'code' => $e->getCode(),
                    'status_code' => $e->getStatusCode(),
                    'message' => $errorMessage,
                    'original_message' => $e->getMessage()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp send failed', [
                'to' => $to,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);

            return (object)[
                'sent' => 'false',
                'error' => (object)[
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Format phone number for Twilio
     * 
     * @param string $phone Phone number
     * @return string Formatted phone number
     */
    protected static function formatPhoneNumber($phone)
    {
        // Remove any existing whatsapp: prefix
        $phone = str_replace('whatsapp:', '', $phone);
        
        // Ensure phone starts with +
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        
        // Add whatsapp: prefix for Twilio
        return 'whatsapp:' . $phone;
    }

    /**
     * Legacy method signature compatibility
     * Maintains backward compatibility with UltraMessage::send() signature
     * 
     * @param string $phone Phone number
     * @param string $activationCode Activation code (will be appended to message)
     * @param string $message Message body
     * @param string $referenceId Optional reference ID
     * @return object Response
     */
    public static function sendLegacy($phone, $activationCode = '', $message = 'لقد تم تسجيل حسابك بنجاح كود التفعيل ', $referenceId = '')
    {
        // Combine message and activation code (matching UltraMessage behavior)
        $fullMessage = $message;
        if (!empty($activationCode)) {
            $fullMessage .= ' ' . $activationCode;
        }

        return self::send($phone, $fullMessage, $referenceId);
    }
}

