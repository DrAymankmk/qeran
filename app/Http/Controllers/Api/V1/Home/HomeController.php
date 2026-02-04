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
        $notificationsCount = 0;
        $unreadNotificationsCount = 0;

        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user();
            $baseQuery = Notification::query()
                ->where('created_at', '>=', $user->created_at)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)->orWhere('user_id', null);
                });

            $notificationsCount = (clone $baseQuery)->count();
            $unreadNotificationsCount = (clone $baseQuery)->unread()->count();

            $unreadNotifications = NotificationResource::collection(
                (clone $baseQuery)
                    ->unread()
                    ->orderBy('created_at', 'desc')
                    ->limit($request->input('notifications_limit', 10))
                    ->get()
            )->resolve();
        }

        return RespondActive::success('The action ran successfully!', [
            'categories' => $categories,
            // 'unread_notifications' => $unreadNotifications,
            'notifications_count' => $notificationsCount,
            'unread_notifications_count' => $unreadNotificationsCount,
        ]);
    }
}