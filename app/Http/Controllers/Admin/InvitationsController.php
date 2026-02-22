<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvitationRequest;
use App\Models\HubFile;
use App\Models\Invitation;
use App\Models\InvitationPackage;
use App\Services\External\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Mpdf\Mpdf;

class InvitationsController extends Controller
{
    /**
     * Display a listing of invitations.
     *
     * @return View
     */
    public function index(Request $request): View|JsonResponse
    {
        try {
            // Check if this is a DataTables server-side processing request
            if ($request->ajax() || $request->has('draw')) {
                return $this->getDataTablesData($request);
            }

            // Regular page load - return view with empty collection
            // DataTables will fetch data via AJAX
            return view('pages.invitation.index', ['invitations' => collect([])]);
        } catch (\Exception $e) {
            Log::error('Error fetching invitations list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax() || $request->has('draw')) {
                return response()->json([
                    'draw' => $request->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => __('admin.error-loading-invitations'),
                ], 500);
            }

            return view('pages.invitation.index', ['invitations' => collect([])])
                ->with('error', __('admin.error-loading-invitations'));
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

        // Build base query with eager loading
        $query = Invitation::with([
            'user:id,country_code,phone',
            'category',
            'hubFiles' => function ($query) {
                $query->whereIn('file_type', [
                    \App\Helpers\Constant::FILE_TYPE['Image'],
                    \App\Helpers\Constant::FILE_TYPE['Audio'],
                    \App\Helpers\Constant::FILE_TYPE['Video'],
                ])
                    ->select('id', 'morphable_id', 'morphable_type', 'file_type', 'file_key', 'path', 'bucket_name')
                    ->orderBy('id', 'desc')
                    ->limit(10);
            },
        ])
            ->whereNotNull('user_id')
            ->select('invitations.id', 'invitations.name', 'invitations.invitation_media_type',
                'invitations.invitation_type', 'invitations.created_at',
                'invitations.user_id', 'invitations.category_id', 'invitations.status');

        // Apply status filter
        $statusFilter = $request->input('status');
        if (! empty($statusFilter)) {
            $query->where('invitations.status', $statusFilter);
        }

        // Apply date range filter
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if (! empty($dateFrom)) {
            $query->whereDate('invitations.created_at', '>=', $dateFrom);
        }
        if (! empty($dateTo)) {
            $query->whereDate('invitations.created_at', '<=', $dateTo);
        }

        // Get total records count (before filtering)
        $totalRecordsQuery = Invitation::whereNotNull('user_id');
        if (! empty($statusFilter)) {
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
                $q->where('id', 'like', '%'.$searchValue.'%')
                    ->orWhere('name', 'like', '%'.$searchValue.'%')
                    ->orWhereRaw('category_id IN (SELECT category_id FROM category_translations WHERE name LIKE ?)', ['%'.$searchValue.'%']);
            });
        }

        // Get filtered count
        $filteredRecords = $query->count();

        // Define column mapping for ordering
        $columns = ['id', 'category_id', 'invitation_type', 'name', 'invitation_media_type', 'created_at'];
        $orderColumnName = $columns[$orderColumn] ?? 'id';

        // Apply ordering
        if ($orderColumnName === 'category_id') {
            // Order by category name requires a join or subquery
            $query->orderBy('invitations.created_at', $orderDir);
        } else {
            $query->orderBy('invitations.'.$orderColumnName, $orderDir);
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

            // Build single media column: image, audio, video (each with type label)
            $imageHtml = $imagePath
                ? '<a target="_blank" href="'.e($imagePath).'"><img class="header-profile-user" src="'.e($imagePath).'" alt="Invitation" loading="lazy" style="width: 50px; height: 50px; object-fit: cover;"></a>'
                : null;
            $audioUrls = $invitation->getAudioUrls();
            $mp3Url = $audioUrls['mp3'];
            $oggUrl = $audioUrls['ogg'];
            $audioHtml = ($mp3Url || $oggUrl)
                ? '<audio controls style="width: 150px; height: 40px;">
                    '.($oggUrl ? '<source src="'.e($oggUrl).'" type="audio/ogg">' : '').'
                    '.($mp3Url ? '<source src="'.e($mp3Url).'" type="audio/mpeg">' : '').'
                    Your browser does not support the audio element.
                </audio>'
                : null;
            $videoPath = null;
            if ($invitation->relationLoaded('hubFiles') && $invitation->hubFiles->isNotEmpty()) {
                $videoFile = $invitation->hubFiles
                    ->where('file_type', \App\Helpers\Constant::FILE_TYPE['Video'])
                    ->sortByDesc('id')
                    ->first();
                $videoPath = $videoFile ? $videoFile->get_path() : null;
            }
            if (! $videoPath) {
                $videoPath = $invitation->designVideo();
            }
            $videoHtml = $videoPath
                ? '<video controls style="width: 120px; height: 80px; object-fit: cover;" preload="metadata"><source src="'.e($videoPath).'" type="video/mp4">Your browser does not support the video tag.</video>'
                : null;

            $mediaParts = [];
            if ($imageHtml) {
                $mediaParts[] = '<div class="mb-1"><span class="badge bg-primary me-1">'.__('admin.media-type-1').'</span>'.$imageHtml.'</div>';
            }
            if ($audioHtml) {
                $mediaParts[] = '<div class="mb-1"><span class="badge bg-info me-1">'.__('admin.media-type-3').'</span>'.$audioHtml.'</div>';
            }
            if ($videoHtml) {
                $mediaParts[] = '<div class="mb-1"><span class="badge bg-success me-1">'.__('admin.media-type-2').'</span>'.$videoHtml.'</div>';
            }
            $mediaColumnHtml = count($mediaParts) > 0 ? implode('', $mediaParts) : __('admin.no-data-available');

            // Format actions HTML
            $whatsappUrl = 'https://api.whatsapp.com/send?phone='.str_replace('+', '', $invitation->user?->country_code ?? '').($invitation->user?->phone ?? '');

            $actionsHtml = '<div class="d-flex gap-3">'.
                '<a href="'.e($whatsappUrl).'" title="'.__('admin.whatsapp').'" class="text-success" target="_blank"><i class="mdi mdi-whatsapp font-size-18"></i></a>'.
                '<a href="javascript:void(0);" onclick="showInvitationDetails('.$invitation->id.')" title="'.__('admin.show').'" class="text-info"><i class="mdi mdi-eye font-size-22"></i></a>';

            // Check permission for edit
            if (Gate::allows('edit-invitations')) {
                $actionsHtml .= '<a href="'.route('invitation.edit', $invitation->id).'" title="'.__('admin.edit').'" class="text-warning"><i class="mdi mdi-file-edit-outline font-size-22"></i></a>';
            }

            $actionsHtml .= '<a href="'.route('invitations.getPackagesByInvitationId', ['invitation_id' => $invitation->id]).'" title="'.__('admin.packages').'" class="text-success"><i class="mdi mdi-package font-size-22"></i></a>'.
                '<a href="'.route('invitation.guards', $invitation->id).'" title="'.__('admin.guards').'" class="text-success"><i class="mdi mdi-account font-size-22"></i></a>';

            // Check permission for delete
            if (Gate::allows('delete-invitations')) {
                $actionsHtml .= '<a onclick="openModalDelete('.$invitation->id.')" title="'.__('admin.delete').'" class="text-danger"><i class="mdi mdi-trash-can-outline font-size-22"></i></a>';
            }

            $actionsHtml .= '</div>';

            $data[] = [
                $invitation->id,
                $invitation->category->name ?? __('admin.no-data-available'),
                __('admin.invitation-type-'.$invitation->invitation_type),
                __('admin.invitation-status-'.$invitation->status),
                $invitation->name,
                __('admin.media-type-'.$invitation->invitation_media_type),
                $mediaColumnHtml,
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

    /**
     * Display guards for a specific invitation.
     */
    public function guards(Invitation $invitation): View
    {
        try {
            // Get guards relationship - pivot data is automatically available
            // No need to eager load 'pivot' as it's not a relationship
            $guards = $invitation->guards()
                ->paginate(config('app.pagination.per_page', 15));

            return view('pages.invitation.guards', compact('guards', 'invitation'));
        } catch (\Exception $e) {
            Log::error('Error fetching guards', [
                'error' => $e->getMessage(),
                'invitation_id' => $invitation->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return view('pages.invitation.guards', [
                'guards' => collect([])->paginate(),
                'invitation' => $invitation,
            ])->with('error', __('admin.error-loading-guards'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // TODO: Enable authorization check
        // $this->authorize('create', Invitation::class);

        return view('pages.invitation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvitationRequest $request): RedirectResponse
    {
        // TODO: Implement store method
        return redirect()->back()->with('error', __('admin.method-not-implemented'));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Use findOrFail for automatic 404 handling
            $invitation = Invitation::with(['user', 'category', 'hubFiles'])
                ->findOrFail($id);

            // Get audio URLs (MP3 and OGG)
            $audioUrls = $invitation->getAudioUrls();

            $data = [
                'invitation_id' => $invitation->id,
                'code' => $invitation->code ?? '',
                'user_name' => $invitation->user?->name ?? '',
                'user_phone' => $invitation->user?->phone ?? '',
                'user_email' => $invitation->user?->email ?? '',
                'invitation_type' => __('admin.invitation-type-'.$invitation->invitation_type),
                'name' => $invitation->name ?? '',
                'media_type' => __('admin.media-type-'.$invitation->invitation_media_type),
                'description' => $invitation->description ?? '',
                'host_name' => $invitation->host_name ?? '',
                'date' => $invitation->date ?? '',
                'time' => $invitation->time ?? '',
                'address' => $invitation->address ?? '',
                'groom' => $invitation->groom ?? '',
                'bride' => $invitation->bride ?? '',
                'event_name' => $invitation->event_name ?? '',
                'status' => __('admin.invitation-status-'.$invitation->status),
                'created_at' => \Carbon\Carbon::parse($invitation->created_at)
                    ->locale(app()->getLocale())
                    ->translatedFormat('l dS F G:i - Y'),
                'design_image' => $invitation->designImage() ?? '',
                'design_video' => $invitation->designVideo() ?? '',
                'design_audio' => $audioUrls['mp3'] ?? '',
                'design_audio_ogg' => $audioUrls['ogg'] ?? '',
                'receipt_image' => $invitation->receiptImage() ?? '',
                'category_name' => $invitation->category?->name ?? '',
            ];

            return response()->json($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Invitation not found', ['invitation_id' => $id]);

            return response()->json(['error' => __('admin.invitation-not-found')], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching invitation details', [
                'error' => $e->getMessage(),
                'invitation_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => __('admin.error-loading-invitation')], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View|RedirectResponse
    {
        // TODO: Enable authorization check
        // $this->authorize('update', Invitation::class);

        try {
            $invitation = Invitation::with(['user', 'category', 'hubFiles'])->findOrFail($id);

            return view('pages.invitation.edit', compact('invitation'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Invitation not found for editing', ['invitation_id' => $id]);

            return redirect()->route('invitation.index')
                ->with('error', __('admin.invitation-not-found'));
        }
    }

    /**
     * Update the specified resource in storage.
     * For large video uploads, ensure server PHP limits are raised (see public/user.ini.example).
     */
    public function update(InvitationRequest $request, int $id): RedirectResponse
    {
        // TODO: Enable authorization check
        // $this->authorize('update', Invitation::class);

        try {
            $invitation = Invitation::findOrFail($id);

            DB::beginTransaction();

            // Fixed bug: longitude was using latitude value
            $invitation->update([
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude ?? $request->latitude, // Fallback if not provided
                'date' => $request->date,
            ]);

            // Handle file upload with proper validation
            if ($request->hasFile('file')) {
                $this->handleFileUpload($request->file('file'), $invitation);
            }

            $invitation->update([
                'status' => Constant::INVITATION_STATUS['Pending user approval'],
            ]);

            // Send notification - Order category: New Order Created
            Notification::notify(
                'users',
                Constant::NOTIFICATIONS_TYPE['Invitation Request'],
                [$invitation->user_id],
                $invitation->id,
                'invitation_confirmation_request',
                [],
                true,
                Constant::NOTIFICATION_CATEGORY['Order'],
                Constant::NOTIFICATION_ORDER_TYPES['New Order Created']
            );

            DB::commit();

            if ($request->invitation_type == Constant::INVITATION_TYPE['Contact Design']) {
                return redirect()
                ->route('invitation-request.index', [
                    'invitation_type' => Constant::INVITATION_TYPE['Contact Design'],
                ])
                ->with('success', __('admin.invitation-updated-successfully'));
            } else {
                return redirect()
                ->route('invitation.index', [
                    'invitation_type' => Constant::INVITATION_TYPE['Contact Design'],
                ])
                ->with('success', __('admin.invitation-updated-successfully'));
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Invitation not found for update', ['invitation_id' => $id]);

            return redirect()->back()->with('error', __('admin.invitation-not-found'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating invitation', [
                'error' => $e->getMessage(),
                'invitation_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', __('admin.error-updating-invitation'));
        }
    }

    /**
     * Delete a single hub file (media) belonging to the invitation.
     */
    public function destroyHubFile(int $invitation, int $hubFile): RedirectResponse
    {
        try {
            $invitationModel = Invitation::findOrFail($invitation);
            $file = HubFile::where('id', $hubFile)
                ->where('morphable_id', $invitation)
                ->where('morphable_type', Invitation::class)
                ->firstOrFail();

            deleteImage($file->get_folder_file(), $file);

            return redirect()->route('invitation.edit', $invitation)
                ->with('success', __('admin.hub-file-deleted'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Hub file or invitation not found', ['invitation_id' => $invitation, 'hub_file_id' => $hubFile]);

            return redirect()->back()->with('error', __('admin.invitation-not-found'));
        } catch (\Exception $e) {
            Log::error('Error deleting invitation hub file', [
                'error' => $e->getMessage(),
                'invitation_id' => $invitation,
                'hub_file_id' => $hubFile,
            ]);

            return redirect()->back()->with('error', __('admin.error-deleting-invitation'));
        }
    }

    /**
     * Replace a hub file (media) with a new upload.
     */
    public function replaceHubFile(Request $request, int $invitation, int $hubFile): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:'.config('app.max_invitation_upload_kb', 102400),
                'mimes:mp4,webm,ogg,mov,avi,jpeg,jpg,png,gif',
                'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo,image/jpeg,image/png,image/gif',
            ],
        ]);

        try {
            $invitationModel = Invitation::findOrFail($invitation);
            $fileModel = HubFile::where('id', $hubFile)
                ->where('morphable_id', $invitation)
                ->where('morphable_type', Invitation::class)
                ->firstOrFail();

            DB::beginTransaction();

            deleteImage($fileModel->get_folder_file(), $fileModel);
            $this->handleFileUpload($request->file('file'), $invitationModel, [
                'file_key' => $fileModel->file_key,
                'file_type' => $fileModel->file_type,
            ]);

            DB::commit();

            return redirect()->route('invitation.edit', $invitation)
                ->with('success', __('admin.hub-file-replaced'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Hub file or invitation not found', ['invitation_id' => $invitation, 'hub_file_id' => $hubFile]);

            return redirect()->back()->with('error', __('admin.invitation-not-found'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error replacing invitation hub file', [
                'error' => $e->getMessage(),
                'invitation_id' => $invitation,
                'hub_file_id' => $hubFile,
            ]);

            return redirect()->back()->with('error', __('admin.error-updating-invitation'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        // TODO: Enable authorization check
        // $this->authorize('delete', Invitation::class);

        try {
            $invitation = Invitation::with('hubFiles')->findOrFail($id);

            DB::beginTransaction();

            // Delete associated files
            if ($invitation->hubFiles->isNotEmpty()) {
                foreach ($invitation->hubFiles as $hubFile) {
                    try {
                        deleteImage($hubFile->get_folder_file(), $hubFile);
                    } catch (\Exception $e) {
                        Log::warning('Error deleting hub file', [
                            'hub_file_id' => $hubFile->id,
                            'error' => $e->getMessage(),
                        ]);
                        // Continue deletion even if file deletion fails
                    }
                }
            }

            $invitation->delete();

            DB::commit();

            return redirect()->back()->with('success', __('admin.invitation-deleted-successfully'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Invitation not found for deletion', ['invitation_id' => $id]);

            return redirect()->back()->with('error', __('admin.invitation-not-found'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting invitation', [
                'error' => $e->getMessage(),
                'invitation_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', __('admin.error-deleting-invitation'));
        }
    }

    /**
     * Change the paid status of an invitation.
     */
    public function changeStatus(int $id): RedirectResponse
    {
    
        try {
            $invitation = Invitation::with('users')->findOrFail($id);

            DB::beginTransaction();

            $newPaidStatus = $invitation->paid == Constant::PAID_STATUS['Paid']
                ? Constant::PAID_STATUS['Not Paid']
                : Constant::PAID_STATUS['Paid'];

            $invitation->update(['paid' => $newPaidStatus]);

            // Send notifications only when status changes to Paid.
            // If push/HTTP fails (e.g. Guzzle/cURL issues), we log the error but DO NOT rollback the paid status.
            if ($newPaidStatus == Constant::PAID_STATUS['Paid']) {
                try {
                    // Notify all users associated with the invitation
                    $userIds = $invitation->users->pluck('id')->toArray();

                    if (! empty($userIds)) {
                        // Order category: New Order Created (invitation received)
                        Notification::notify(
                            'users',
                            Constant::NOTIFICATIONS_TYPE['Invitations'],
                            $userIds,
                            $invitation->id,
                            'invitation_received',
                            [],
                            true,
                            Constant::NOTIFICATION_CATEGORY['Order'],
                            Constant::NOTIFICATION_ORDER_TYPES['New Order Created']
                        );
                    }

                    // Notify the invitation owner - Payment category: New Payment Received
                    Notification::notify(
                        'users',
                        Constant::NOTIFICATIONS_TYPE['Invitations'],
                        [$invitation->user_id],
                        $invitation->id,
                        'payment_approved',
                        [],
                        true,
                        Constant::NOTIFICATION_CATEGORY['Payment'],
                        Constant::NOTIFICATION_PAYMENT_TYPES['New Payment Received']
                    );
                } catch (\Throwable $e) {
                    Log::error('Error sending payment notifications after status change', [
                        'error' => $e->getMessage(),
                        'invitation_id' => $id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Intentionally swallow the exception so the status change remains committed.
                }
            }

            DB::commit();

            return redirect()->back()->with('success', __('admin.status-updated-successfully'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Invitation not found for status change', ['invitation_id' => $id]);

            return redirect()->back()->with('error', __('admin.invitation-not-found'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error changing invitation status', [
                'error' => $e->getMessage(),
                'invitation_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', __('admin.error-updating-status'));
        }
    }

    /**
     * Get packages by invitation ID.
     */
    public function getPackagesByInvitationId(Request $request): View|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'invitation_id' => 'required|integer|exists:invitations,id',
            ]);

            $invitationPackages = InvitationPackage::with(['package', 'invitation.user'])
                ->where('invitation_id', $validated['invitation_id'])
                ->orderBy('created_at', 'desc')
                ->paginate(config('app.pagination.per_page', 15));

            return view('pages.invitation.packages', compact('invitationPackages'));
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error fetching invitation packages', [
                'error' => $e->getMessage(),
                'invitation_id' => $request->invitation_id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', __('admin.error-loading-packages'));
        }
    }

    /**
     * Change the status of an invitation package.
     */
    public function changePackageStatus(Request $request): RedirectResponse
    {
        // TODO: Enable authorization check
        // $this->authorize('update', InvitationPackage::class);

        try {
            $validated = $request->validate([
                'invitation_package_id' => 'required|integer|exists:invitation_package,id',
                'status' => 'required|integer|in:' . implode(',', array_values(Constant::PAID_STATUS)),
            ]);

            $invitationPackage = InvitationPackage::with('invitation.user')
                ->findOrFail($validated['invitation_package_id']);

            DB::beginTransaction();

            $newPackageStatus = (int) $validated['status'];
            $invitationPackage->update(['status' => $newPackageStatus]);

            // Recompute invitation.paid from ALL packages using Constant::PAID_STATUS (1=Paid, 2=Not Paid, 3=Pending, 4=Rejected)
            $invitation = $invitationPackage->invitation;
            $packageStatuses = InvitationPackage::where('invitation_id', $invitation->id)
                ->pluck('status');

            $newInvitationPaidStatus = $this->resolveInvitationPaidFromPackageStatuses($packageStatuses);
            Invitation::where('id', $invitation->id)->update(['paid' => $newInvitationPaidStatus]);

            // Send notification when payment is approved - Payment category: New Payment Received
            if ($newPackageStatus === Constant::PAID_STATUS['Paid']) {
                Notification::notify(
                    'users',
                    Constant::NOTIFICATIONS_TYPE['Invitations'],
                    [$invitationPackage->invitation->user_id],
                    $invitationPackage->invitation_id,
                    'payment_approved',
                    [],
                    true,
                    Constant::NOTIFICATION_CATEGORY['Payment'],
                    Constant::NOTIFICATION_PAYMENT_TYPES['New Payment Received']
                );
            }

            DB::commit();

            return redirect()->back()->with('success', __('admin.package-status-updated-successfully'));
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Invitation package not found', [
                'package_id' => $request->invitation_package_id ?? null,
            ]);

            return redirect()->back()->with('error', __('admin.package-not-found'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error changing package status', [
                'error' => $e->getMessage(),
                'package_id' => $request->invitation_package_id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', __('admin.error-updating-package-status'));
        }
    }

    /**
     * Resolve invitation.paid from package statuses (Constant::PAID_STATUS).
     * Priority: Paid (1) > Pending Admin Payment (3) > Rejected (4) > Not Paid (2).
     *
     * @param  \Illuminate\Support\Collection<int, int>  $packageStatuses
     * @return int
     */
    protected function resolveInvitationPaidFromPackageStatuses($packageStatuses): int
    {
        $statuses = $packageStatuses->unique()->values();

        if ($statuses->contains(Constant::PAID_STATUS['Paid'])) {
            return Constant::PAID_STATUS['Paid'];
        }
        if ($statuses->contains(Constant::PAID_STATUS['Pending Admin Payment'])) {
            return Constant::PAID_STATUS['Pending Admin Payment'];
        }
        if ($statuses->contains(Constant::PAID_STATUS['Rejected'])) {
            return Constant::PAID_STATUS['Rejected'];
        }

        return Constant::PAID_STATUS['Not Paid'];
    }

    /**
     * Handle file upload based on file type.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  array{file_key?: int, file_type?: int}  $overrides  Optional file_key/file_type (e.g. when replacing a hub file).
     *
     * @throws \Exception
     */
    protected function handleFileUpload($file, Invitation $invitation, array $overrides = []): void
    {
        $mimeType = $file->getMimeType();
        $fileConfig = [
            'value' => $file,
            'folderName' => Constant::INVITATION_IMAGE_FOLDER_NAME,
            'model' => $invitation,
            'saveInDatabase' => true,
            'file_key' => $overrides['file_key'] ?? Constant::FILE_KEY['Main'],
            'file_type' => $overrides['file_type'] ?? Constant::FILE_TYPE['Image'],
        ];

        if (str_contains($mimeType, 'video/')) {
            $fileConfig['folderName'] = Constant::INVITATION_VIDEO_FOLDER_NAME;
            $fileConfig['file_type'] = Constant::FILE_TYPE['Video'];
            storeVideo($fileConfig);
        } elseif ($mimeType === 'image/gif') {
            storeGif($fileConfig);
        } else {
            $fileConfig['extension'] = $file->getClientOriginalExtension();
            storeImage($fileConfig);
        }
    }

     public function invitationsExportPdf()
     {
         $invitations = Invitation::whereNotNull('user_id')->orderBy('created_at', 'desc')->get();

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
         $html = view('pages.invitation.pdf-export', compact('invitations'))->render();

         $mpdf->WriteHTML($html);

         $filename = 'invitations_'.date('Y-m-d_His').'.pdf';

         return $mpdf->Output($filename, 'D'); // D = Download
     }
}