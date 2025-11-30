<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Constant;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class InvitationRequestController extends Controller
{


        /**
     * Display invitations by type and status.
     *
     * @return View|RedirectResponse
     *
     * @throws ValidationException
     */
    public function index(Request $request): View|RedirectResponse|JsonResponse
    {
        try {
            // Check if this is a DataTables server-side processing request
            if ($request->ajax() || $request->has('draw')) {
                // For AJAX requests, get invitation_type from request
                $invitationType = $request->input('invitation_type');

                // Validate invitation_type for AJAX requests
                if (! $invitationType || ! in_array($invitationType, array_values(Constant::INVITATION_TYPE))) {
                    return response()->json([
                        'draw' => $request->input('draw', 1),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => __('admin.invalid-invitation-type'),
                    ], 400);
                }

                return $this->getRequestDataTablesData($request, $invitationType);
            }

            // Regular page load - validate invitation_type
            $validated = $request->validate([
                'invitation_type' => 'required|integer|in:'.implode(',', array_values(Constant::INVITATION_TYPE)),
            ]);
            $invitationType = $validated['invitation_type'];

            if ($invitationType == Constant::INVITATION_TYPE['Contact Design']) {
                // Return view with empty collection - DataTables will fetch data via AJAX
                return view('pages.invitation-request.index', ['invitations' => collect([])]);
            }

            return redirect()->back()->with('error', __('admin.invalid-invitation-type'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error fetching invitation requests', [
                'error' => $e->getMessage(),
                'invitation_type' => $request->invitation_type ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax() || $request->has('draw')) {
                return response()->json([
                    'draw' => $request->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => __('admin.error-loading-requests'),
                ], 500);
            }

            return redirect()->back()->with('error', __('admin.error-loading-requests'));
        }
    }

    /**
     * Handle DataTables server-side processing for invitation requests
     */
    private function getRequestDataTablesData(Request $request, int $invitationType): JsonResponse
    {
        // Get DataTables parameters
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value', '');
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');

        // Build base query with eager loading
        $query = Invitation::with([
            'user:id,country_code,phone',
            'category',
            'hubFiles' => function ($query) {
                $query->where('file_type', \App\Helpers\Constant::FILE_TYPE['Image'])
                    ->select('id', 'morphable_id', 'morphable_type', 'file_type', 'file_key', 'path', 'bucket_name')
                    ->orderBy('id', 'desc')
                    ->limit(5);
            },
        ])
            ->where('invitation_type', $invitationType)
            ->whereNotNull('user_id')
            ->whereIn('status', [
                Constant::INVITATION_STATUS['Pending admin'],
                Constant::INVITATION_STATUS['Rejected'],
            ])
            ->select('id', 'name', 'invitation_media_type', 'invitation_type', 'created_at', 'user_id', 'category_id', 'status');

        // Apply status filter (within allowed statuses)
        $statusFilter = $request->input('status');
        if (! empty($statusFilter) && in_array($statusFilter, [
            Constant::INVITATION_STATUS['Pending admin'],
            Constant::INVITATION_STATUS['Rejected'],
        ])) {
            $query->where('status', $statusFilter);
        }

        // Apply date range filter
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if (! empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (! empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Get total records count (before filtering)
        $totalRecordsQuery = Invitation::where('invitation_type', $invitationType)
            ->whereNotNull('user_id')
            ->whereIn('status', [
                Constant::INVITATION_STATUS['Pending admin'],
                Constant::INVITATION_STATUS['Rejected'],
            ]);
        if (! empty($statusFilter) && in_array($statusFilter, [
            Constant::INVITATION_STATUS['Pending admin'],
            Constant::INVITATION_STATUS['Rejected'],
        ])) {
            $totalRecordsQuery->where('status', $statusFilter);
        }
        if (! empty($dateFrom)) {
            $totalRecordsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if (! empty($dateTo)) {
            $totalRecordsQuery->whereDate('created_at', '<=', $dateTo);
        }
        $totalRecords = $totalRecordsQuery->count();

        // Apply search filter
        if (! empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('invitations.id', 'like', '%'.$searchValue.'%')
                ->orWhere('invitations.name', 'like', '%'.$searchValue.'%')
                ->orWhereRaw('invitations.category_id IN (SELECT category_id FROM category_translations WHERE name LIKE ? OR title LIKE ?)', ['%'.$searchValue.'%', '%'.$searchValue.'%']);

            });
        }

        // Get filtered count
        $filteredRecords = $query->count();

        // Define column mapping for ordering
        $columns = ['id', 'category_id', 'name', 'invitation_media_type', 'created_at'];
        $orderColumnName = $columns[$orderColumn] ?? 'id';

        // Apply ordering
        if ($orderColumnName === 'category_id') {
            $query->orderBy('created_at', $orderDir);
        } else {
            $query->orderBy($orderColumnName, $orderDir);
        }

        // Apply pagination
        $invitations = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = [];
        foreach ($invitations as $invitation) {
            // Get image path
            $imagePath = null;
            if ($invitation->relationLoaded('hubFiles') && $invitation->hubFiles->isNotEmpty()) {
                $imageFile = $invitation->hubFiles
                    ->where('file_type', \App\Helpers\Constant::FILE_TYPE['Image'])
                    ->sortByDesc('id')
                    ->first();

                if (! $imageFile) {
                    $imageFile = $invitation->hubFiles
                        ->where('file_type', \App\Helpers\Constant::FILE_TYPE['Image'])
                        ->where('file_key', \App\Helpers\Constant::FILE_KEY['Not Main'])
                        ->first();
                }

                $imagePath = $imageFile ? $imageFile->get_path() : null;
            } else {
                $imagePath = $invitation->designImage() ?: $invitation->getMainImagePath();
            }

            // Format image HTML
            $imageHtml = $imagePath
                ? '<a target="_blank" href="'.e($imagePath).'"><img class="header-profile-user" src="'.e($imagePath).'" alt="Invitation" loading="lazy" style="width: 50px; height: 50px; object-fit: cover;"></a>'
                : __('admin.no-data-available');

            // Format actions HTML
            $whatsappUrl = 'https://api.whatsapp.com/send?phone='.str_replace('+', '', $invitation->user?->country_code ?? '').($invitation->user?->phone ?? '');

            $actionsHtml = '<div class="d-flex gap-3">'.
                '<a href="'.e($whatsappUrl).'" title="'.__('admin.whatsapp').'" class="text-success" target="_blank"><i class="mdi mdi-whatsapp font-size-22"></i></a>'.
                '<a href="javascript:void(0);" onclick="showInvitationRequestDetails('.$invitation->id.')" title="'.__('admin.show').'" class="text-info"><i class="mdi mdi-eye font-size-22"></i></a>';

            // Check permission for edit using Gate
            if (Gate::allows('edit-invitations')) {
                $actionsHtml .= '<a href="'.route('invitation.edit', $invitation->id).'" title="'.__('admin.edit').'" class="text-warning"><i class="mdi mdi-file-edit-outline font-size-22"></i></a>';
            }

            $actionsHtml .= '<a href="'.route('invitations.getPackagesByInvitationId', ['invitation_id' => $invitation->id]).'" title="'.__('admin.packages').'" class="text-success"><i class="mdi mdi-package font-size-18"></i></a>';

            // Check permission for delete using Gate
            if (Gate::allows('delete-invitations')) {
                $actionsHtml .= '<a onclick="openModalDelete('.$invitation->id.')" title="'.__('admin.delete').'" class="text-danger"><i class="mdi mdi-trash-can-outline font-size-22"></i></a>';
            }

            $actionsHtml .= '</div>';

            $data[] = [
                $invitation->id,
                $invitation->category->name ?? __('admin.no-data-available'),
                $invitation->name,
                __('admin.media-type-'.$invitation->invitation_media_type),
                $imageHtml,
                \Carbon\Carbon::parse($invitation->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y'),
                $actionsHtml,
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }
    //
    public function invitationRequestExportPdf(){
         $invitationRequests = Invitation::where('invitation_type', Constant::INVITATION_TYPE['Contact Design'])->orderBy('created_at', 'desc')->get();

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
        $html = view('pages.invitation-request.pdf-export', compact('invitationRequests'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'invitation-requests_' . date('Y-m-d_His') . '.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }
}