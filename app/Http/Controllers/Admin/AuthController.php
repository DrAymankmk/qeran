<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\Invitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('login.login');
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login.form')->with('error', 'Email Or Password not correct');
        }
    }

    public function dashboard(Request $request)
    {
        // Get filter parameter (today, week, month, year, all)
        $filter = $request->get('filter', 'all');

        // Get custom date range if provided
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get date range based on filter or custom dates
        $dateRange = $this->getDateRange($filter, $fromDate, $toDate);

        // Get counts with date filter
        $usersQuery = User::query();
        $invitationsQuery = Invitation::query();
        $contactUsQuery = ContactUs::query();

        if ($dateRange['start'] && $dateRange['end']) {
            $usersQuery->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            $invitationsQuery->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            $contactUsQuery->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }

        $usersCount = $usersQuery->count();
        $invitationsCount = $invitationsQuery->count();
        $contactUsCount = $contactUsQuery->count();

        // Get recent invitations
        $invitations = Invitation::orderBy('created_at', 'desc')
            ->with('user')
            ->whereHas('user')
            ->when($dateRange['start'] && $dateRange['end'], function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->take(10)->get();

        // Get statistics data based on filter
        $statistics = $this->getStatisticsData($filter);

        // Get most used categories
        $categoriesData = $this->getMostUsedCategories($dateRange);

        // Get request invitations
        $requestInvitations = Invitation::where('status', Constant::INVITATION_STATUS['Pending admin'])
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->whereHas('user')
            ->when($dateRange['start'] && $dateRange['end'], function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->take(10)
            ->get();

        $requestInvitationsCount = Invitation::where('status', Constant::INVITATION_STATUS['Pending admin'])
            ->count();

        // Get contact us
        $contactUs = ContactUs::orderBy('created_at', 'desc')
            ->when($dateRange['start'] && $dateRange['end'], function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->take(10)
            ->get();

        return view('pages.dashboard.index', array_merge([
            'usersCount' => $usersCount,
            'invitationsCount' => $invitationsCount,
            'contactUsCount' => $contactUsCount,
            'invitations' => $invitations,
            'requestInvitations' => $requestInvitations,
            'requestInvitationsCount' => $requestInvitationsCount,
            'contactUs' => $contactUs,
            'filter' => $filter,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'categoriesData' => $categoriesData,
        ], $statistics));
    }

    /**
     * Get date range based on filter or custom dates
     */
    private function getDateRange($filter, $fromDate = null, $toDate = null)
    {
        // If custom dates are provided, use them
        if ($fromDate && $toDate) {
            try {
                return [
                    'start' => Carbon::parse($fromDate)->startOfDay(),
                    'end' => Carbon::parse($toDate)->endOfDay(),
                ];
            } catch (\Exception $e) {
                // If date parsing fails, fall back to filter
            }
        }

        $now = Carbon::now();

        switch ($filter) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                ];
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                ];
            case 'all':
            default:
                return [
                    'start' => null,
                    'end' => null,
                ];
        }
    }

    /**
     * Get statistics data for charts based on filter
     */
    private function getStatisticsData($filter)
    {
        $verifiedUsers = [];
        $notVerifiedUsers = [];
        $invitationsAppDesign = [];
        $invitationsContactDesign = [];
        $invitationsUserDesign = [];
        $categories = [];

        $now = Carbon::now();

        // Determine number of periods based on filter
        switch ($filter) {
            case 'today':
                // 24 hours
                $periods = 24;
                $periodType = 'hour';
                break;
            case 'week':
                // 7 days
                $periods = 7;
                $periodType = 'day';
                break;
            case 'month':
                // 30 days
                $periods = 30;
                $periodType = 'day';
                break;
            case 'year':
                // 12 months
                $periods = 12;
                $periodType = 'month';
                break;
            case 'all':
            default:
                // 12 months
                $periods = 12;
                $periodType = 'month';
                break;
        }

        // Generate data for each period
        for ($i = $periods - 1; $i >= 0; $i--) {
            $periodStart = $now->copy();
            $periodEnd = $now->copy();

            if ($periodType === 'hour') {
                $periodStart->subHours($i)->startOfHour();
                $periodEnd->subHours($i)->endOfHour();
            } elseif ($periodType === 'day') {
                $periodStart->subDays($i)->startOfDay();
                $periodEnd->subDays($i)->endOfDay();
            } else { // month
                $periodStart->subMonths($i)->startOfMonth();
                $periodEnd->subMonths($i)->endOfMonth();
            }

            // Get verified users count
            $verifiedUsers[] = User::where('verified', 2)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->count();

            // Get not verified users count
            $notVerifiedUsers[] = User::where('verified', 0)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->count();

            // Get invitations by type
            $invitationsAppDesign[] = Invitation::where('invitation_type', Constant::INVITATION_TYPE['App Design'])
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->count();

            $invitationsContactDesign[] = Invitation::where('invitation_type', Constant::INVITATION_TYPE['Contact Design'])
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->count();

            $invitationsUserDesign[] = Invitation::where('invitation_type', Constant::INVITATION_TYPE['User Design'])
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->count();

            // Store period labels for chart
            if ($periodType === 'hour') {
                $categories[] = $periodStart->format('H:i');
            } elseif ($periodType === 'day') {
                $categories[] = $periodStart->format('M d');
            } else { // month
                $categories[] = $periodStart->locale('ar')->translatedFormat('F');
            }
        }

        return [
            'verifiedUsers' => $verifiedUsers,
            'notVerifiedUsers' => $notVerifiedUsers,
            'invitationsAppDesign' => $invitationsAppDesign,
            'invitationsContactDesign' => $invitationsContactDesign,
            'invitationsUserDesign' => $invitationsUserDesign,
            'categories' => $categories,
            'periodType' => $periodType,
        ];
    }

    /**
     * Get most used categories statistics
     */
    private function getMostUsedCategories($dateRange)
    {
        $query = Invitation::selectRaw('category_id, COUNT(*) as count')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->orderBy('count', 'desc')
            ->limit(10);

        if ($dateRange['start'] && $dateRange['end']) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }

        $categoryStats = $query->get();

        $categoriesData = [];
        foreach ($categoryStats as $stat) {
            $category = \App\Models\Category::with('translations')->find($stat->category_id);
            if ($category) {
                $categoriesData[] = [
                    'name' => $category->name ?? 'Unknown',
                    'count' => $stat->count,
                ];
            }
        }

        return $categoriesData;
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login.form');
    }

    /**
     * Show the admin profile page
     */
    public function profile()
    {
        $admin = auth('admin')->user();
        return view('pages.admin.profile', compact('admin'));
    }

    /**
     * Update the admin profile
     */
    public function updateProfile(\App\Http\Requests\Admin\ProfileRequest $request)
    {
        $admin = auth('admin')->user();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $admin->update($data);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($admin->hubFiles()->exists()) {
                deleteImage($admin->hubFiles->get_folder_file(), $admin->hubFiles());
            }

            // Store new image
            storeImage([
                'value' => $request->file('image'),
                'folderName' => \App\Helpers\Constant::ADMIN_IMAGE_FOLDER_NAME,
                'model' => $admin,
                'saveInDatabase' => true
            ]);
        }

        return redirect()->route('admin.profile')->with('success', __('admin.profile-updated-successfully'));
    }
}
