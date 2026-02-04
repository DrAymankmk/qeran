<?php

namespace App\Http\Controllers\Api\V1\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Notifications\NotificationResource;
use App\Models\Category;
use App\Models\Notification;
use App\Services\RespondActive;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $categories = CategoryResource::collection(
            Category::GetActiveCategories()->paginate()
        )->response()->getData();

        $unreadNotifications = [];
        if (auth('sanctum')->check()) {
            $unreadNotifications = NotificationResource::collection(
                Notification::query()
                    ->where('created_at', '>=', auth('sanctum')->user()->created_at)
                    ->where(function ($query) {
                        $query->where('user_id', auth('sanctum')->id())
                            ->orWhere('user_id', null);
                    })
                    ->unread()
                    ->orderBy('created_at', 'desc')
                    ->limit($request->input('notifications_limit', 10))
                    ->get()
            )->resolve();
        }

        return RespondActive::success('The action ran successfully!', [
            'categories' => $categories,
            'unread_notifications' => $unreadNotifications,
            'unread_notifications_count' => is_array($unreadNotifications)
                ? count($unreadNotifications)
                : 0,
        ]);
    }
}
