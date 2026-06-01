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
            Log::warning('Baileys receipt webhook: unauthorized', [
                'has_token' => $token !== null && $token !== '',
            ]);

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'sessionId' => 'required|string|max:128',
            'messageId' => 'required|string|max:128',
            'referenceId' => 'nullable|string|max:255',
            'to' => 'nullable|string|max:32',
            'status' => 'required|in:delivered,read',
            'source' => 'nullable|string|max:64',
        ]);

        Log::info('Baileys receipt webhook received', [
            'session_id' => $validated['sessionId'],
            'message_id' => $validated['messageId'],
            'reference_id' => $validated['referenceId'] ?? null,
            'status' => $validated['status'],
            'source' => $validated['source'] ?? null,
        ]);

        $log = $this->findContactLog($validated['messageId'], $validated['referenceId'] ?? null);

        if (! $log) {
            Log::warning('Baileys receipt webhook: no invitation_contact_log row', [
                'message_id' => $validated['messageId'],
                'reference_id' => $validated['referenceId'] ?? null,
            ]);

            return response()->json(['ok' => true, 'ignored' => true, 'reason' => 'log_not_found']);
        }

        $updates = [
            'whatsapp_message_id' => $validated['messageId'],
        ];

        $applied = [];

        if ($validated['status'] === 'delivered' && ! $log->delivered_at) {
            $updates['delivered_at'] = now();
            $applied[] = 'delivered_at';
        }

        if ($validated['status'] === 'read') {
            if (! $log->read_at) {
                $updates['read_at'] = now();
                $applied[] = 'read_at';
            }
            if (! $log->delivered_at) {
                $updates['delivered_at'] = now();
                $applied[] = 'delivered_at_implicit';
            }
        }

        if (count($applied) === 0) {
            Log::info('Baileys receipt webhook: no new timestamps (already set)', [
                'contact_log_id' => $log->id,
                'status' => $validated['status'],
                'delivered_at' => $log->delivered_at?->toIso8601String(),
                'read_at' => $log->read_at?->toIso8601String(),
            ]);

            return response()->json([
                'ok' => true,
                'contact_log_id' => $log->id,
                'status' => $validated['status'],
                'applied' => [],
            ]);
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

        Log::info('Baileys receipt webhook: updated contact log', [
            'contact_log_id' => $log->id,
            'status' => $validated['status'],
            'applied' => $applied,
            'message_id' => $validated['messageId'],
        ]);

        return response()->json([
            'ok' => true,
            'contact_log_id' => $log->id,
            'status' => $validated['status'],
            'applied' => $applied,
        ]);
    }

    protected function findContactLog(string $messageId, ?string $referenceId): ?InvitationContactLog
    {
        if ($referenceId !== null && $referenceId !== '') {
            $byRef = InvitationContactLog::query()
                ->where('reference_id', $referenceId)
                ->orderByDesc('id')
                ->first();

            if ($byRef) {
                return $byRef;
            }
        }

        return InvitationContactLog::query()
            ->where('whatsapp_message_id', $messageId)
            ->orderByDesc('id')
            ->first();
    }
}
