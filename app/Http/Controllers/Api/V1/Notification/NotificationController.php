<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\NotificationResource;
use App\Models\Notification;
use App\Services\RespondActive;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
