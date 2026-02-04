<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\NotificationResource;
use App\Models\Notification;
use App\Services\RespondActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['destroy', 'read', 'readAll', 'pusherAuth']);
    }

    /**
     * List notifications. Authenticated: own + broadcast, unread first. Guest: broadcast only.
     */
    public function index(Request $request)
    {
        if (auth('sanctum')->check()) {
            $query = Notification::query()
                ->where('created_at', '>=', auth('sanctum')->user()->created_at)
                ->where(function ($query) {
                    $query->where('user_id', auth('sanctum')->id())
                        ->orWhere('user_id', null);
                })
                ->orderByReadStatus();

            auth('sanctum')->user()->update(['notification_count' => 0]);

            $notifications = $query->paginate($request->input('per_page', 15));
        } else {
            $notifications = Notification::query()
                ->where('user_id', null)
                ->orderBy('created_at', 'desc')
                ->paginate($request->input('per_page', 15));
        }

        $data = NotificationResource::collection($notifications)->response()->getData();

        return RespondActive::success('Notifications fetched successfully!', $data);
    }

    /**
     * Mark a single notification as read.
     *
     * If the notification has a specific owner (user_id != null) and the
     * authenticated user ID is known, we ensure they match. If the auth ID
     * is null (guard mismatch), we don't block the action.
     */
    public function read($id)
    {
        $notification = Notification::findOrFail($id);

        $authId = auth('sanctum')->id();
        $ownerId = $notification->user_id;

        // Debug log to trace authorization issues
        Log::info('API notifications.read attempt', [
            'notification_id' => $notification->id,
            'notification_user_id' => $ownerId,
            'notification_user_id_type' => gettype($ownerId),
            'auth_id' => $authId,
            'auth_id_type' => gettype($authId),
            'auth_guard' => 'sanctum',
        ]);

        if (!is_null($authId)
            && !is_null($ownerId)
            && (int)$ownerId !== (int)$authId) {
            return RespondActive::clientError('Unauthorized', [], 403);
        }

        // Explicitly mark as read and persist, then refresh the model
        if (is_null($notification->read_at)) {
            $notification->read_at = now();
            $notification->save();
        }

        $notification->refresh();

        $data = (new NotificationResource($notification))->resolve();

        return RespondActive::success('Notification marked as read.', $data);
    }

    /**
     * Mark all notifications for the current user as read.
     */
    public function readAll()
    {
        $updated = Notification::query()
            ->where(function ($query) {
                $query->where('user_id', auth('sanctum')->id())
                    ->orWhere('user_id', null);
            })
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return RespondActive::success('All notifications marked as read.', ['count' => $updated]);
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== null && $notification->user_id !== auth('sanctum')->id()) {
            return RespondActive::clientError('Unauthorized', [], 403);
        }

        $notification->delete();

        return RespondActive::success('Notification deleted successfully!');
    }

    /**
     * Get Pusher authentication for private channels
     */
    public function pusherAuth(Request $request)
    {
        $user = auth('sanctum')->user();
        
        if (!$user) {
            return RespondActive::clientError('Unauthorized', [], 401);
        }

        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );

            $channelName = $request->input('channel_name');
            $socketId = $request->input('socket_id');

            // Only allow users to subscribe to their own channel
            $allowedChannel = 'users-' . $user->id;
            if ($channelName !== $allowedChannel && $channelName !== 'presence-users-' . $user->id) {
                return RespondActive::clientError('Unauthorized channel', [], 403);
            }

            $auth = $pusher->socket_auth($channelName, $socketId);

            return response($auth, 200)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return RespondActive::clientError('Authentication failed: ' . $e->getMessage(), [], 500);
        }
    }
}
