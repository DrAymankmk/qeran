<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationsRequest;
use App\Models\Notification;
use App\Services\External\Notification as PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class NotificationsController extends Controller
{

    public function index(Request $request)
    {
//        abort_if(Gate::denies('access_notifications'), 403);
        try {
            // Check if this is a DataTables server-side processing request
            if ($request->ajax() || $request->has('draw')) {
                return $this->getDataTablesData($request);
            }

            // Get category from request (optional)
            $category = $request->get('category');

            // Get counts for each category
            $categoryCounts = [
                'all' => Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin'])->count(),
                Constant::NOTIFICATION_CATEGORY['Order'] => Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin'])
                    ->where('category', Constant::NOTIFICATION_CATEGORY['Order'])->count(),
                Constant::NOTIFICATION_CATEGORY['Payment'] => Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin'])
                    ->where('category', Constant::NOTIFICATION_CATEGORY['Payment'])->count(),
                Constant::NOTIFICATION_CATEGORY['User'] => Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin'])
                    ->where('category', Constant::NOTIFICATION_CATEGORY['User'])->count(),
                Constant::NOTIFICATION_CATEGORY['Contact Us'] => Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin'])
                    ->where('category', Constant::NOTIFICATION_CATEGORY['Contact Us'])->count(),
            ];

            // Regular page load - return view with empty collection
            // DataTables will fetch data via AJAX
            return view('pages.notifications.index', compact('category', 'categoryCounts'));
        } catch (\Exception $e) {
            Log::error('Error fetching notifications list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax() || $request->has('draw')) {
                return response()->json([
                    'draw' => $request->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => __('admin.error-loading-notifications'),
                ], 500);
            }

            return view('pages.notifications.index', ['category' => null, 'categoryCounts' => []])
                ->with('error', __('admin.error-loading-notifications'));
        }
    }

    /**
     * Handle DataTables server-side processing
     */
    private function getDataTablesData(Request $request): JsonResponse
    {
        // Get DataTables parameters
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');

        // Get category filter
        $category = $request->input('category');

        // Build base query - filter by admin notifications
        $query = Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin']);

        // Filter by category if provided
        if ($category && in_array($category, array_values(Constant::NOTIFICATION_CATEGORY))) {
            $query->where('category', $category);
        }

        // Get total records count (before filtering)
        $totalRecordsQuery = Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin']);
        if ($category && in_array($category, array_values(Constant::NOTIFICATION_CATEGORY))) {
            $totalRecordsQuery->where('category', $category);
        }
        $totalRecords = $totalRecordsQuery->count();

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('title', 'like', '%' . $searchValue . '%')
                    ->orWhere('description', 'like', '%' . $searchValue . '%');
            });
        }

        // Get filtered count (before ordering and pagination)
        $filteredRecords = $query->count();

        // Apply ordering
        // For column-specific ordering, we need to handle it differently
        if ($orderColumn == 0 && $orderDir == 'desc') {
            // Default: order by read status (unread first), then by created_at DESC
            $query->orderByReadStatus();
        } elseif ($orderColumn == 0) {
            // ID column (ascending)
            $query->orderBy('id', $orderDir);
        } elseif ($orderColumn == 1) {
            // Status column (read/unread) - order by read_at
            if ($orderDir == 'asc') {
                $query->orderByRaw('read_at IS NULL ASC, read_at ASC');
            } else {
                $query->orderByRaw('read_at IS NULL DESC, read_at DESC');
            }
        } elseif ($orderColumn == 6) {
            // Created at column
            $query->orderBy('created_at', $orderDir);
        } else {
            // Default: order by read status (unread first), then by created_at DESC
            $query->orderByReadStatus();
        }

        // Apply pagination
        $notifications = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = [];
        foreach ($notifications as $notification) {
            $isRead = $notification->read_at !== null;
            $categoryText = '';
            $typeText = '';
            $redirectUrl = null;

            // Get category text
            if ($notification->category) {
                switch ($notification->category) {
                    case Constant::NOTIFICATION_CATEGORY['Order']:
                        $categoryText = __('admin.order_notifications');
                        if ($notification->target_id) {
                            $redirectUrl = route('invitation.index', ['highlight' => $notification->target_id]);
                        }
                        break;
                    case Constant::NOTIFICATION_CATEGORY['Payment']:
                        $categoryText = __('admin.payment_notifications');
                        if ($notification->target_id) {
                            $redirectUrl = route('financial.index', ['highlight' => $notification->target_id]);
                        }
                        break;
                    case Constant::NOTIFICATION_CATEGORY['User']:
                        $categoryText = __('admin.user_notifications');
                        if ($notification->target_id) {
                            $redirectUrl = route('users.index', ['highlight' => $notification->target_id]);
                        }
                        break;
                    case Constant::NOTIFICATION_CATEGORY['Contact Us']:
                        $categoryText = __('admin.contact_us_notification');
                        if ($notification->target_id) {
                            $redirectUrl = route('contact.index', ['highlight' => $notification->target_id]);
                        }
                        break;
                }
            }

            // Get type text
            if ($notification->notification_type && $notification->category) {
                $types = [];
                switch ($notification->category) {
                    case Constant::NOTIFICATION_CATEGORY['Order']:
                        $types = Constant::NOTIFICATION_ORDER_TYPES;
                        break;
                    case Constant::NOTIFICATION_CATEGORY['Payment']:
                        $types = Constant::NOTIFICATION_PAYMENT_TYPES;
                        break;
                    case Constant::NOTIFICATION_CATEGORY['User']:
                        $types = Constant::NOTIFICATION_USER_TYPES;
                        break;
                    case Constant::NOTIFICATION_CATEGORY['Contact Us']:
                        $types = Constant::NOTIFICATION_CONTACT_TYPES;
                        break;
                }

                if (!empty($types)) {
                    $typeKey = array_search($notification->notification_type, $types);
                    if ($typeKey !== false) {
                        $typeText = __('admin.' . strtolower(str_replace(' ', '_', $typeKey)));
                    }
                }
            }

            // Build title with link if redirectUrl exists
            $titleHtml = $redirectUrl
                ? '<a href="' . $redirectUrl . '" class="text-primary text-decoration-none">' . e($notification->title) . '</a>'
                : e($notification->title);

            // Build actions HTML
            $actionsHtml = '<div class="d-flex gap-3">';
            $actionsHtml .= '<a href="javascript:void(0);" onclick="showNotificationDetails(' . $notification->id . ')" title="' . __('admin.view_details') . '" class="text-info"><i class="mdi mdi-information font-size-18"></i></a>';

            if ($redirectUrl) {
                $actionsHtml .= '<a href="' . $redirectUrl . '" title="' . __('admin.view_related_item') . '" class="text-success"><i class="mdi mdi-open-in-new font-size-18"></i></a>';
            }

            // Only show eye button if notification is unread
            // if (!$isRead) {
            //     $actionsHtml .= '<a href="' . route('notifications.show', $notification->id) . '" title="' . __('admin.view_notification') . '" class="text-primary eye-btn-' . $notification->id . '"><i class="mdi mdi-eye font-size-18"></i></a>';
            // }

            $actionsHtml .= '<a onclick="openModalDelete(' . $notification->id . ')" title="' . __('admin.delete') . '" class="text-danger"><i class="mdi mdi-delete font-size-18"></i></a>';
            $actionsHtml .= '</div>';

            $data[] = [
                $notification->id,
                $isRead
                    ? '<span class="badge bg-success">' . __('admin.read') . '</span>'
                    : '<span class="badge bg-danger">' . __('admin.unread') . '</span>',
                $categoryText ?: __('admin.no-data-available'),
                $typeText ?: __('admin.no-data-available'),
                $titleHtml,
                e($notification->description),
                $notification->created_at,
                $actionsHtml,
            ];
        }

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function create()
    {
//        abort_if(Gate::denies('create_notifications'), 403);
        return view('pages.notifications.create');

    }


    public function store(NotificationsRequest $request)
    {
//        abort_if(Gate::denies('create_notifications'), 403);
        // Notification::create($request->validated()+['type'=>Constant::NOTIFICATIONS_TYPE['Admin']]);
        PushNotificationService::notifyFor('users',$request->ar['title'],$request->ar['description']);
        return redirect()->route('notifications.index')->with('success','Added');


    }


    public function show($id)
    {
        $notification = Notification::whereId($id)->firstOrFail();

        // Mark as read when viewing
        $notification->markAsRead();

        // Get the URL to redirect based on notification category and target_id
        $redirectUrl = $this->getNotificationRedirectUrl($notification);

        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        return redirect()->route('notifications.index')
            ->with('info', __('admin.notification-viewed'));
    }


    public function getDetails($id)
    {
        $notification = Notification::whereId($id)->firstOrFail();

        // Mark as read when viewing details
        if (!$notification->isRead()) {
            $notification->markAsRead();
        }

        // Get category and type text
        $categoryText = '';
        $typeText = '';

        if ($notification->category) {
            switch ($notification->category) {
                case Constant::NOTIFICATION_CATEGORY['Order']:
                    $categoryText = __('admin.order_notifications');
                    break;
                case Constant::NOTIFICATION_CATEGORY['Payment']:
                    $categoryText = __('admin.payment_notifications');
                    break;
                case Constant::NOTIFICATION_CATEGORY['User']:
                    $categoryText = __('admin.user_notifications');
                    break;
                case Constant::NOTIFICATION_CATEGORY['Contact Us']:
                    $categoryText = __('admin.contact_us_notification');
                    break;
            }
        }

        if ($notification->notification_type && $notification->category) {
            switch ($notification->category) {
                case Constant::NOTIFICATION_CATEGORY['Order']:
                    $types = Constant::NOTIFICATION_ORDER_TYPES;
                    break;
                case Constant::NOTIFICATION_CATEGORY['Payment']:
                    $types = Constant::NOTIFICATION_PAYMENT_TYPES;
                    break;
                case Constant::NOTIFICATION_CATEGORY['User']:
                    $types = Constant::NOTIFICATION_USER_TYPES;
                    break;
                case Constant::NOTIFICATION_CATEGORY['Contact Us']:
                    $types = Constant::NOTIFICATION_CONTACT_TYPES;
                    break;
                default:
                    $types = [];
            }

            if (isset($types)) {
                $typeKey = array_search($notification->notification_type, $types);
                if ($typeKey !== false) {
                    $typeText = __('admin.' . strtolower(str_replace(' ', '_', $typeKey)));
                }
            }
        }

        // Get notification type name
        $notificationTypeName = '';
        if ($notification->type !== null) {
            $typeKey = array_search($notification->type, Constant::NOTIFICATIONS_TYPE);
            if ($typeKey !== false) {
                $notificationTypeName = __('admin.' . strtolower(str_replace(' ', '_', $typeKey)));
            }
        }

        // Helper function to clean UTF-8 strings
        $cleanString = function($str) {
            if (is_null($str) || $str === '') {
                return null;
            }
            // Convert to string if not already
            $str = (string)$str;

            // Remove invalid UTF-8 characters
            $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');

            // Remove any remaining invalid UTF-8 sequences
            $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str);

            // Final UTF-8 validation
            if (!mb_check_encoding($str, 'UTF-8')) {
                // If still invalid, use iconv to clean
                $str = @iconv('UTF-8', 'UTF-8//IGNORE', $str) ?: '';
            }

            return $str;
        };

        // Format dates safely
        $formatDate = function($date) {
            if (!$date) {
                return null;
            }
            try {
                if ($date instanceof \DateTime) {
                    return $date->format('Y-m-d H:i:s');
                }
                return Carbon::parse($date)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        };

        // Format dates with localization
        $formatDateLocalized = function($date) use ($formatDate) {
            if (!$date) {
                return null;
            }
            try {
                if ($date instanceof \DateTime) {
                    $carbon = Carbon::instance($date);
                } else {
                    $carbon = Carbon::parse($date);
                }
                return $carbon->locale(app()->getLocale())->translatedFormat('l dS F Y G:i');
            } catch (\Exception $e) {
                return $formatDate($date); // Fallback to simple format
            }
        };

        try {
            $data = [
                'id' => $notification->id,
                'type' => $notification->type,
                'type_name' => $cleanString($notificationTypeName),
                'category' => $notification->category,
                'category_text' => $cleanString($categoryText),
                'notification_type' => $notification->notification_type,
                'notification_type_text' => $cleanString($typeText),
                'user_id' => $notification->user_id,
                'target_id' => $notification->target_id,
                'title' => $cleanString($notification->title),
                'description' => $cleanString($notification->description),
                'read_at' => $formatDate($notification->read_at),
                'read_at_formatted' => $formatDateLocalized($notification->read_at),
                'created_at' => $formatDate($notification->created_at),
                'created_at_formatted' => $formatDateLocalized($notification->created_at),
                'updated_at' => $formatDate($notification->updated_at),
                'is_read' => $notification->isRead(),
            ];

            // Test JSON encoding before returning
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                // If encoding fails, clean all strings more aggressively
                array_walk_recursive($data, function(&$value) {
                    if (is_string($value)) {
                        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
                    }
                });
            }

            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Error encoding notification details', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            // Return minimal safe data
            return response()->json([
                'id' => $notification->id,
                'error' => 'Error loading notification details',
                'title' => mb_convert_encoding((string)$notification->title, 'UTF-8', 'UTF-8'),
                'description' => mb_convert_encoding((string)$notification->description, 'UTF-8', 'UTF-8'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::whereId($id)->firstOrFail();
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => __('admin.notification-marked-as-read')
        ]);
    }

    /**
     * Get recent notifications for dropdown
     */
    public function getRecent()
    {
        // Use DB query to get raw created_at to avoid accessor issues
        $notificationsData = DB::table('notifications')
            ->where('type', Constant::NOTIFICATIONS_TYPE['Admin'])
            ->orderByRaw('read_at IS NULL DESC, created_at DESC')
            ->limit(5)
            ->get();

        $formattedNotifications = $notificationsData->map(function ($notificationData) {
            // Get the full notification model for other attributes
            $notification = Notification::find($notificationData->id);

            if (!$notification) {
                return null;
            }
            // Get category and type text
            $categoryText = '';
            $typeText = '';

            if ($notification->category) {
                switch ($notification->category) {
                    case Constant::NOTIFICATION_CATEGORY['Order']:
                        $categoryText = __('admin.order_notifications');
                        break;
                    case Constant::NOTIFICATION_CATEGORY['Payment']:
                        $categoryText = __('admin.payment_notifications');
                        break;
                    case Constant::NOTIFICATION_CATEGORY['User']:
                        $categoryText = __('admin.user_notifications');
                        break;
                    case Constant::NOTIFICATION_CATEGORY['Contact Us']:
                        $categoryText = __('admin.contact_us_notification');
                        break;
                }
            }

            if ($notification->notification_type && $notification->category) {
                switch ($notification->category) {
                    case Constant::NOTIFICATION_CATEGORY['Order']:
                        $types = Constant::NOTIFICATION_ORDER_TYPES;
                        break;
                    case Constant::NOTIFICATION_CATEGORY['Payment']:
                        $types = Constant::NOTIFICATION_PAYMENT_TYPES;
                        break;
                    case Constant::NOTIFICATION_CATEGORY['User']:
                        $types = Constant::NOTIFICATION_USER_TYPES;
                        break;
                    case Constant::NOTIFICATION_CATEGORY['Contact Us']:
                        $types = Constant::NOTIFICATION_CONTACT_TYPES;
                        break;
                    default:
                        $types = [];
                }

                if (isset($types)) {
                    $typeKey = array_search($notification->notification_type, $types);
                    if ($typeKey !== false) {
                        $typeText = __('admin.' . strtolower(str_replace(' ', '_', $typeKey)));
                    }
                }
            }

            // Use raw created_at from DB query (bypasses model accessor)
            $createdAtRaw = $notificationData->created_at;
            $createdAtFormatted = '';
            $createdAtIso = null;

            try {
                if ($createdAtRaw) {
                    $carbon = Carbon::parse($createdAtRaw);
                    $createdAtFormatted = $carbon->locale(app()->getLocale())->diffForHumans();
                    $createdAtIso = $carbon->toIso8601String();
                }
            } catch (\Exception $e) {
                // Fallback to simple format if parsing fails
                $createdAtFormatted = $createdAtRaw ? date('Y-m-d H:i', strtotime($createdAtRaw)) : '';
                $createdAtIso = $createdAtRaw ? date('c', strtotime($createdAtRaw)) : null;
            }

            // Get redirect URL based on category and target_id
            $redirectUrl = null;
            if ($notification->category && $notification->target_id) {
                $redirectUrl = $this->getNotificationRedirectUrlForCategory($notification->category, $notification->target_id);
            }

            // Fallback to notification show if no redirect URL
            if (!$redirectUrl) {
                $redirectUrl = route('notifications.show', $notification->id);
            }

            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'description' => $notification->description,
                'category' => $categoryText,
                'type' => $typeText,
                'is_read' => $notification->isRead(),
                'target_id' => $notification->target_id,
                'category_id' => $notification->category,
                'created_at' => $createdAtFormatted,
                'created_at_raw' => $createdAtIso,
                'url' => $redirectUrl,
            ];
        })->filter(); // Remove null entries

        $unreadCount = Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin'])
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $formattedNotifications->values(), // Re-index array
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('type', Constant::NOTIFICATIONS_TYPE['Admin'])
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => __('admin.all-notifications-marked-as-read')
        ]);
    }

    /**
     * Get redirect URL based on notification category and type
     */
    private function getNotificationRedirectUrl($notification)
    {
        if (!$notification->category || !$notification->target_id) {
            return null;
        }

        return $this->getNotificationRedirectUrlForCategory($notification->category, $notification->target_id);
    }

    /**
     * Get redirect URL for a specific category and target ID
     */
    private function getNotificationRedirectUrlForCategory($category, $targetId)
    {
        switch ($category) {
            case Constant::NOTIFICATION_CATEGORY['Order']:
                // For orders (invitations), redirect to invitation index page with highlight
                return route('invitation.index', ['highlight' => $targetId]);

            case Constant::NOTIFICATION_CATEGORY['Payment']:
                // For payments, redirect to financial page with highlight
                return route('financial.index', ['highlight' => $targetId]);

            case Constant::NOTIFICATION_CATEGORY['User']:
                // For users, redirect to users index page with highlight
                return route('users.index', ['highlight' => $targetId]);

            case Constant::NOTIFICATION_CATEGORY['Contact Us']:
                // For contact us, redirect to contact index page with highlight
                return route('contact.index', ['highlight' => $targetId]);

            default:
                return null;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
//        abort_if(Gate::denies('edit_notifications'), 403);
        $notification=Notification::whereId($id)->first();
        return view('pages.notifications.edit',compact('notification'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NotificationsRequest $request, Notification $notification)
    {
//        abort_if(Gate::denies('edit_notifications'), 403);
        $notification->update($request->validated());
        return redirect()->route('notifications.index')->with('success','Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
//        abort_if(Gate::denies('delete_notifications'), 403);
        $notification->delete();
        return redirect()->route('notifications.index')->with('success','Deleted');

    }


    public function notificationsExportPdf()
    {
        $notifications = Notification::orderByReadStatus()->get();

        // Configure mPDF with Arabic font support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape
            'orientation' => 'L',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'autoLangToFont' => true, // Automatically detect and use appropriate fonts
            'autoScriptToLang' => true, // Automatically set language
            'autoVietnamese' => true,
            'autoArabic' => true, // Enable Arabic support
            'direction' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // Build HTML content
        $html = view('pages.notifications.pdf-export', compact('notifications'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'notifications_' . date('Y-m-d_His') . '.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }
}
