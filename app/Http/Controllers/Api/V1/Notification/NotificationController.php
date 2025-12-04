<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\NotificationResource;
use App\Models\Notification;
use App\Services\RespondActive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Pusher\Pusher;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('destroy');
    }

    public function index(Request $request)
    {
        switch (auth('sanctum')->check()) {
            case true:
                $notifications =
                    Notification::query()
                        ->where('created_at', '>=', auth('sanctum')->user()->created_at)
                        ->where(function ($query) {
                            $query->where('user_id', auth('sanctum')->id())->orwhere('user_id', null);
                        })
                        ->orderBy('created_at', 'desc');
                auth('sanctum')->user()->update(['notification_count' => 0]);

                break;
            case false:
                $notifications = Notification::where('user_id', null)->where('created_at', '>=', Carbon::now());
        }

        $notifications = NotificationResource::collection((clone $notifications)->latest('id')->paginate())->response()->getData();

        return RespondActive::success('The action ran successfully!', $notifications);
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return RespondActive::success('The action ran successfully!');
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
