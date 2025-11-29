<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\InvitationPackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class FinancialController extends Controller
{
    /**
     * Display a listing of financial transactions.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = InvitationPackage::with(['invitation.user', 'package'])
            ->orderBy('created_at', 'desc');

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // Default to showing only paid payments if no status filter is specified
            if (! $request->filled('status')) {
                $query->where('status', Constant::PAID_STATUS['Paid']);
            }
        }

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('customer_name')) {
            $query->whereHas('invitation.user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->customer_name.'%');
            });
        }

        $payments = $query->paginate(20);

        // Get statistics for charts (always use paid status for stats)
        $stats = $this->getStatistics($request);

        return view('pages.financial.index', compact('payments', 'stats'));
    }

    /**
     * Get monthly financial report.
     *
     * @return \Illuminate\View\View
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $query = InvitationPackage::with(['invitation.user', 'package'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc');

             // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // Default to showing only paid payments if no status filter is specified
            if (! $request->filled('status')) {
                $query->where('status', Constant::PAID_STATUS['Paid']);
            }
        }

        $payments = $query->get();
        $totalAmount = $payments->sum('price');
        $totalOrders = $payments->count();
        $totalCustomers = $payments->pluck('invitation.user_id')->unique()->count();

        // Daily breakdown
        $dailyData = $payments->groupBy(function ($payment) {
            return Carbon::parse($payment->created_at)->format('Y-m-d');
        })->map(function ($group) {
            return [
                'date' => Carbon::parse($group->first()->created_at)->format('Y-m-d'),
                'amount' => $group->sum('price'),
                'orders' => $group->count(),
            ];
        })->values();

        return view('pages.financial.monthly-report', compact('payments', 'totalAmount', 'totalOrders', 'totalCustomers', 'dailyData', 'month'));
    }

    /**
     * Get annual financial report.
     *
     * @return \Illuminate\View\View
     */
    public function annualReport(Request $request)
    {
        $year = $request->input('year', now()->format('Y'));
        $startDate = Carbon::parse($year.'-01-01')->startOfYear();
        $endDate = Carbon::parse($year.'-12-31')->endOfYear();

        $query = InvitationPackage::with(['invitation.user', 'package'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc');

            // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // Default to showing only paid payments if no status filter is specified
            if (! $request->filled('status')) {
                $query->where('status', Constant::PAID_STATUS['Paid']);
            }
        }
        $payments = $query->get();
        $totalAmount = $payments->sum('price');
        $totalOrders = $payments->count();
        $totalCustomers = $payments->pluck('invitation.user_id')->unique()->count();

        // Monthly breakdown
        $monthlyData = $payments->groupBy(function ($payment) {
            return Carbon::parse($payment->created_at)->format('Y-m');
        })->map(function ($group) {
            return [
                'month' => Carbon::parse($group->first()->created_at)->format('Y-m'),
                'amount' => $group->sum('price'),
                'orders' => $group->count(),
            ];
        })->values();

        return view('pages.financial.annual-report', compact('payments', 'totalAmount', 'totalOrders', 'totalCustomers', 'monthlyData', 'year'));
    }

    /**
     * Export financial report to Excel (via DataTables)
     * This will be handled by DataTables Excel export button
     */
    public function exportExcel(Request $request)
    {
        // DataTables handles Excel export client-side
        // This method can be used for server-side Excel export if needed
        return redirect()->back();
    }

    /**
     * Export financial report to PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = InvitationPackage::with(['invitation.user', 'package'])
            ->orderBy('created_at', 'desc');

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // Default to showing only paid payments if no status filter is specified
            if (! $request->filled('status')) {
                $query->where('status', Constant::PAID_STATUS['Paid']);
            }
        }

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('customer_name')) {
            $query->whereHas('invitation.user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->customer_name.'%');
            });
        }

        $payments = $query->get();
        $totalAmount = $payments->sum('price');
        $totalOrders = $payments->count();

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
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'autoVietnamese' => true,
            'autoArabic' => true,
            'direction' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // Build HTML content
        $html = view('pages.financial.pdf-export', compact('payments', 'totalAmount', 'totalOrders'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'financial_report_'.date('Y-m-d_His').'.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }

    /**
     * Get statistics for charts and dashboard.
     *
     * @return array
     */
    private function getStatistics(Request $request = null)
    {
        $dateFrom = $request && $request->input('date_from') ? Carbon::parse($request->date_from) : Carbon::now()->subYear();
        $dateTo = $request && $request->input('date_to') ? Carbon::parse($request->date_to) : Carbon::now();

        // Get all users who registered in the date range
        $allUsers = User::whereBetween('created_at', [$dateFrom, $dateTo])->pluck('id');

        // Total customers (users who have at least one paid package)
        $customerIds = InvitationPackage::where('status', Constant::PAID_STATUS['Paid'])
            ->whereHas('invitation', function ($query) use ($allUsers) {
                $query->whereIn('user_id', $allUsers);
            })
            ->with('invitation:id,user_id')
            ->get()
            ->pluck('invitation.user_id')
            ->unique()
            ->filter();

        $totalCustomers = $customerIds->count();

        // Total visitors (all users minus customers)
        $totalVisitors = $allUsers->count() - $totalCustomers;

        // Conversion rate
        $totalUsers = $totalVisitors + $totalCustomers;
        $conversionRate = $totalUsers > 0 ? ($totalCustomers / $totalUsers) * 100 : 0;

        // Monthly orders data
        $monthlyOrders = InvitationPackage::where('status', Constant::PAID_STATUS['Paid'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Daily orders data (last 30 days)
        $dailyOrders = InvitationPackage::where('status', Constant::PAID_STATUS['Paid'])
            ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Fill missing months/days with 0
        $months = [];
        $current = Carbon::parse($dateFrom)->startOfMonth();
        while ($current <= Carbon::parse($dateTo)) {
            $monthKey = $current->format('Y-m');
            $months[$monthKey] = $monthlyOrders[$monthKey] ?? 0;
            $current->addMonth();
        }

        $days = [];
        $currentDay = Carbon::now()->subDays(30);
        while ($currentDay <= Carbon::now()) {
            $dayKey = $currentDay->format('Y-m-d');
            $days[$dayKey] = $dailyOrders[$dayKey] ?? 0;
            $currentDay->addDay();
        }

        return [
            'totalVisitors' => $totalVisitors,
            'totalCustomers' => $totalCustomers,
            'conversionRate' => round($conversionRate, 2),
            'monthlyOrders' => $months,
            'dailyOrders' => $days,
            'monthlyOrdersLabels' => array_keys($months),
            'monthlyOrdersData' => array_values($months),
            'dailyOrdersLabels' => array_keys($days),
            'dailyOrdersData' => array_values($days),
        ];
    }

    /**
     * Get chart data via AJAX.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        $stats = $this->getStatistics($request);

        return response()->json($stats);
    }
}
