<?php

namespace App\Http\Controllers\Webhook;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\InvitationContactLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaileysMessageStatusController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $secret = (string) config('services.baileys.gateway_secret');
        $token = $request->bearerToken() ?? $request->header('X-Baileys-Secret');

        if ($secret === '' || ! hash_equals($secret, (string) $token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'sessionId' => 'required|string|max:128',
            'messageId' => 'required|string|max:128',
            'referenceId' => 'required|string|max:255',
            'to' => 'nullable|string|max:32',
            'status' => 'required|in:delivered,read',
        ]);

        $log = InvitationContactLog::query()
            ->where(function ($query) use ($validated) {
                $query->where('reference_id', $validated['referenceId'])
                    ->orWhere('whatsapp_message_id', $validated['messageId']);
            })
            ->orderByDesc('id')
            ->first();

        if (! $log) {
            return response()->json(['ok' => true, 'ignored' => true]);
        }

        $updates = [
            'whatsapp_message_id' => $validated['messageId'],
        ];

        if ($validated['status'] === 'delivered' && ! $log->delivered_at) {
            $updates['delivered_at'] = now();
        }

        if ($validated['status'] === 'read') {
            $updates['read_at'] = now();
            if (! $log->delivered_at) {
                $updates['delivered_at'] = now();
            }
        }

        $log->update($updates);

        if ($log->user_id && $log->invitation_id) {
            $seen = $validated['status'] === 'read'
                ? Constant::SEEN_STATUS['seen']
                : Constant::SEEN_STATUS['delivered'];

            DB::table('invitation_user')
                ->where('invitation_id', $log->invitation_id)
                ->where('user_id', $log->user_id)
                ->update(['seen' => $seen]);
        }

        Log::info('Baileys message receipt', [
            'contact_log_id' => $log->id,
            'status' => $validated['status'],
            'message_id' => $validated['messageId'],
        ]);

        return response()->json([
            'ok' => true,
            'contact_log_id' => $log->id,
            'status' => $validated['status'],
        ]);
    }
}
