<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\External\Notification;
use App\Services\RespondActive;
use App\Helpers\Constant;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsAppController extends Controller
{
    /**
     * Handle incoming WhatsApp webhook
     */
    public function handle(Request $request)
    {
        try {
            // Log incoming webhook for debugging
            Log::info('WhatsApp Webhook Received', [
                'headers' => $request->headers->all(),
                'body' => $request->all()
            ]);

            // Get webhook event type
            $eventType = $request->input('event_type');
            
            // Only handle message_ack events for now
            if ($eventType !== 'message_ack') {
                Log::info('WhatsApp Webhook: Ignoring event type', ['event_type' => $eventType]);
                return response()->json(['status' => 'ignored'], 200);
            }

            // Handle message acknowledgment
            return $this->handleMessageAcknowledgment($request);
            // return response()->json(['status' => 'done'], 200);

        } catch (\Exception $e) {
            Log::error('WhatsApp Webhook Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Handle message acknowledgment (read receipts, delivery status)
     */
    private function handleMessageAcknowledgment(Request $request)
    {
        $data = $request->input('data', []);


        
        // Extract relevant information
        $referenceId = $request->referenceId ?? '';
        $invitationId = null;
        $userId = null;
        
        // Split referenceId to get invitation ID and user ID
        if (!empty($referenceId) && str_contains($referenceId, '-')) {
            $parts = explode('-', $referenceId, 2);
            $invitationId = isset($parts[0]) ? (int)$parts[0] : null;
            $userId = isset($parts[1]) ? (int)$parts[1] : null;
        }
        
        $messageInfo = [
            'message_id' => $data['id'] ?? '',
            'referenceId' => $referenceId,
            'invitation_id' => $invitationId,
            'user_id' => $userId,
            'from' => $data['from'] ?? '',
            'to' => $data['to'] ?? '',
            'author' => $data['author'] ?? '',
            'pushname' => $data['pushname'] ?? '',
            'ack_status' => $data['ack'] ?? '',
            'type' => $data['type'] ?? '',
            'body' => $data['body'] ?? '',
            'fromMe' => $data['fromMe'] ?? false,
            'timestamp' => $data['time'] ?? now()->timestamp,
            'instance_id' => $request->input('instanceId', ''),
            'hash' => $request->input('hash', '')
        ];

        // Log the acknowledgment
        Log::info('WhatsApp Message Acknowledgment', $messageInfo);

        // Handle different acknowledgment statuses
        switch ($messageInfo['ack_status']) {
            case 'server':
                $this->handleMessageSent($messageInfo);
                break;
            case 'device':
                $this->handleMessageDelivered($messageInfo);
                break;
            case 'read':
                $this->handleMessageRead($messageInfo);
                break;
            default:
                Log::info('WhatsApp: Unknown ack status', ['status' => $messageInfo['ack_status']]);
        }

        // Store the acknowledgment data if needed
        $this->storeAcknowledgment($messageInfo);

        return response()->json(['status' => 'acknowledgment_processed'], 200);
    }

    /**
     * Handle message sent acknowledgment
     */
    private function handleMessageSent($messageInfo)
    {
        Log::info('WhatsApp: Message sent', [
            'message_id' => $messageInfo['message_id'],
            'to' => $messageInfo['to']
        ]);
        
        $invitation = Invitation::find($messageInfo['invitation_id']);
        $invitation->users()->where('user_id', $messageInfo['user_id'])->update(['seen' => Constant::SEEN_STATUS['Sent']]);
        
        // Update message status to 'sent' in your database if needed
        // Example: Message::where('whatsapp_id', $messageInfo['message_id'])->update(['status' => 'sent']);
    }

    /**
     * Handle message delivered acknowledgment
     */
    private function handleMessageDelivered($messageInfo)
    {
        Log::info('WhatsApp: Message delivered', [
            'message_id' => $messageInfo['message_id'],
            'to' => $messageInfo['to']
        ]);

        $invitation = Invitation::find($messageInfo['invitation_id']);
        $invitation->users()->where('user_id', $messageInfo['user_id'])->update(['seen' => Constant::SEEN_STATUS['delivered']]);
        
        // Update message status to 'delivered' in your database if needed
        // Example: Message::where('whatsapp_id', $messageInfo['message_id'])->update(['status' => 'delivered']);
    }

    /**
     * Handle message read acknowledgment
     */
    private function handleMessageRead($messageInfo)
    {
        Log::info('WhatsApp: Message read', [
            'message_id' => $messageInfo['message_id'],
            'to' => $messageInfo['to'],
            'pushname' => $messageInfo['pushname']
        ]);
        
        
        $invitation = Invitation::find($messageInfo['invitation_id']);
        $invitation->users()->where('user_id', $messageInfo['user_id'])->update(['seen' => Constant::SEEN_STATUS['seen']]);
        
    }

    /**
     * Store acknowledgment data (optional)
     */
    private function storeAcknowledgment($messageInfo)
    {
        // You can implement this to store acknowledgment data in your database
        // This might be useful for analytics or tracking message delivery rates
        
        // Example implementation:
        // WhatsAppAcknowledgment::create([
        //     'message_id' => $messageInfo['message_id'],
        //     'from' => $messageInfo['from'],
        //     'to' => $messageInfo['to'],
        //     'ack_status' => $messageInfo['ack_status'],
        //     'timestamp' => Carbon::createFromTimestamp($messageInfo['timestamp']),
        //     'instance_id' => $messageInfo['instance_id'],
        //     'hash' => $messageInfo['hash']
        // ]);
    }

    /**
     * Verify webhook signature for security
     */
    private function verifyWebhookSignature(Request $request)
    {
        // Signature verification removed as requested
        return true;
    }

    /**
     * Handle incoming WhatsApp messages
     */
    private function handleIncomingMessage(Request $request)
    {
        $messageData = $this->extractMessageData($request);
        
        // Find user by phone number
        $user = $this->findUserByPhone($messageData['from']);
        
        if (!$user) {
            Log::info('WhatsApp: Message from unknown user', ['phone' => $messageData['from']]);
            return response()->json(['status' => 'user_not_found'], 200);
        }

        // Process message based on content
        $response = $this->processMessage($user, $messageData);
        
        // Store message in database if needed
        $this->storeIncomingMessage($user, $messageData);

        return response()->json(['status' => 'processed', 'response' => $response], 200);
    }

    /**
     * Handle message status updates (sent, delivered, read)
     */
    private function handleMessageStatus(Request $request)
    {
        $statusData = $this->extractStatusData($request);
        
        // Update message status in database
        $this->updateMessageStatus($statusData);
        
        Log::info('WhatsApp: Message status updated', $statusData);
        
        return response()->json(['status' => 'updated'], 200);
    }

    /**
     * Handle delivery receipts
     */
    private function handleDeliveryReceipt(Request $request)
    {
        $receiptData = $this->extractReceiptData($request);
        
        // Update delivery status
        $this->updateDeliveryStatus($receiptData);
        
        return response()->json(['status' => 'receipt_processed'], 200);
    }

    /**
     * Process incoming message and generate appropriate response
     */
    private function processMessage($user, $messageData)
    {
        $messageText = strtolower(trim($messageData['body']));
        
        // Handle different message types
        switch (true) {
            case str_contains($messageText, 'help') || str_contains($messageText, 'مساعدة'):
                return $this->sendHelpMessage($user);
                
            case str_contains($messageText, 'invitations') || str_contains($messageText, 'دعوات'):
                return $this->sendUserInvitations($user);
                
            case str_contains($messageText, 'status') || str_contains($messageText, 'حالة'):
                return $this->sendInvitationStatus($user, $messageText);
                
            case is_numeric($messageText):
                // Handle invitation ID or verification code
                return $this->handleNumericInput($user, $messageText);
                
            default:
                return $this->sendDefaultResponse($user);
        }
    }

    /**
     * Send help message to user
     */
    private function sendHelpMessage($user)
    {
        $helpMessage = "مرحباً بك في Modern Invitation! 🎉\n\n";
        $helpMessage .= "الأوامر المتاحة:\n";
        $helpMessage .= "• اكتب 'دعوات' لعرض دعواتك\n";
        $helpMessage .= "• اكتب 'حالة [رقم الدعوة]' لمعرفة حالة دعوة معينة\n";
        $helpMessage .= "• اكتب 'مساعدة' لعرض هذه الرسالة\n";
        
        $this->sendWhatsAppMessage($user->country_code . $user->phone, $helpMessage);
        
        return ['action' => 'help_sent'];
    }

    /**
     * Send user's invitations
     */
    private function sendUserInvitations($user)
    {
        $invitations = Invitation::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('category')->limit(5)->get();

        if ($invitations->isEmpty()) {
            $message = "لا توجد دعوات متاحة حالياً.";
        } else {
            $message = "دعواتك الحالية:\n\n";
            foreach ($invitations as $invitation) {
                $status = $this->getInvitationStatusText($invitation->status);
                $message .= "🎊 {$invitation->event_name}\n";
                $message .= "📅 {$invitation->date} في {$invitation->time}\n";
                $message .= "📍 {$invitation->address}\n";
                $message .= "🔢 رقم الدعوة: {$invitation->id}\n";
                $message .= "📋 الحالة: {$status}\n\n";
            }
        }

        $this->sendWhatsAppMessage($user->country_code . $user->phone, $message);
        
        return ['action' => 'invitations_sent', 'count' => $invitations->count()];
    }

    /**
     * Send invitation status
     */
    private function sendInvitationStatus($user, $messageText)
    {
        // Extract invitation ID from message
        preg_match('/\d+/', $messageText, $matches);
        
        if (empty($matches)) {
            $this->sendWhatsAppMessage($user->country_code . $user->phone, 
                "يرجى كتابة رقم الدعوة بشكل صحيح. مثال: حالة 123");
            return ['action' => 'invalid_format'];
        }

        $invitationId = $matches[0];
        $invitation = Invitation::find($invitationId);

        if (!$invitation || !$invitation->users()->where('user_id', $user->id)->exists()) {
            $this->sendWhatsAppMessage($user->country_code . $user->phone, 
                "لم يتم العثور على دعوة برقم {$invitationId}");
            return ['action' => 'invitation_not_found'];
        }

        $status = $this->getInvitationStatusText($invitation->status);
        $message = "📋 حالة الدعوة رقم {$invitationId}:\n\n";
        $message .= "🎊 {$invitation->event_name}\n";
        $message .= "📅 {$invitation->date} في {$invitation->time}\n";
        $message .= "📍 {$invitation->address}\n";
        $message .= "📋 الحالة: {$status}\n";

        $this->sendWhatsAppMessage($user->country_code . $user->phone, $message);
        
        return ['action' => 'status_sent', 'invitation_id' => $invitationId];
    }

    /**
     * Handle numeric input (invitation ID or verification code)
     */
    private function handleNumericInput($user, $input)
    {
        // Check if it's a verification code
        if (strlen($input) <= 6) {
            // Handle verification code logic here
            return ['action' => 'verification_code_processed'];
        }

        // Treat as invitation ID
        return $this->sendInvitationStatus($user, "status " . $input);
    }

    /**
     * Send default response for unrecognized messages
     */
    private function sendDefaultResponse($user)
    {
        $message = "لم أتمكن من فهم رسالتك. 🤔\n";
        $message .= "اكتب 'مساعدة' لعرض الأوامر المتاحة.";
        
        $this->sendWhatsAppMessage($user->country_code . $user->phone, $message);
        
        return ['action' => 'default_response'];
    }

    /**
     * Send WhatsApp message using existing UltraMessage service
     */
    private function sendWhatsAppMessage($phone, $message)
    {
        return \App\Services\External\UltraMessage::send($phone, '', $message);
    }

    /**
     * Find user by phone number
     */
    private function findUserByPhone($phone)
    {
        // Remove country code and clean phone number
        $cleanPhone = preg_replace('/[^\d]/', '', $phone);
        
        return User::where(function($query) use ($cleanPhone, $phone) {
            $query->where('phone', $cleanPhone)
                  ->orWhere('country_code', '+' . substr($phone, 0, -10))
                  ->where('phone', substr($phone, -10));
        })->first();
    }

    /**
     * Get invitation status text in Arabic
     */
    private function getInvitationStatusText($status)
    {
        $statusMap = [
            Constant::INVITATION_STATUS['Pending user approval'] => 'في انتظار الموافقة',
            Constant::INVITATION_STATUS['Approved'] => 'مقبولة',
            Constant::INVITATION_STATUS['Cancelled'] => 'ملغية',
            Constant::INVITATION_STATUS['Completed'] => 'مكتملة'
        ];

        return $statusMap[$status] ?? 'غير محدد';
    }

    /**
     * Store incoming message in database (optional)
     */
    private function storeIncomingMessage($user, $messageData)
    {
        // Implement message storage if needed
        // You might want to create a WhatsAppMessage model
    }

    /**
     * Update message status in database
     */
    private function updateMessageStatus($statusData)
    {
        // Implement status update logic
    }

    /**
     * Update delivery status
     */
    private function updateDeliveryStatus($receiptData)
    {
        // Implement delivery status update logic
    }

    /**
     * Extract message data from webhook request
     */
    private function extractMessageData(Request $request)
    {
        // Adapt this based on your WhatsApp provider's webhook format
        return [
            'from' => $request->input('messages.0.from', ''),
            'body' => $request->input('messages.0.text.body', ''),
            'timestamp' => $request->input('messages.0.timestamp', now()),
            'message_id' => $request->input('messages.0.id', ''),
            'type' => $request->input('messages.0.type', 'text')
        ];
    }

    /**
     * Extract status data from webhook request
     */
    private function extractStatusData(Request $request)
    {
        return [
            'message_id' => $request->input('statuses.0.id', ''),
            'status' => $request->input('statuses.0.status', ''),
            'timestamp' => $request->input('statuses.0.timestamp', now()),
            'recipient_id' => $request->input('statuses.0.recipient_id', '')
        ];
    }

    /**
     * Extract receipt data from webhook request
     */
    private function extractReceiptData(Request $request)
    {
        return [
            'message_id' => $request->input('delivery.message_id', ''),
            'status' => $request->input('delivery.status', ''),
            'timestamp' => $request->input('delivery.timestamp', now())
        ];
    }
} 
