<?php

namespace App\Http\Controllers\Api\V1\Invitation;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Invitation\CheckInvitationRequest;
use App\Http\Requests\Api\V1\Invitation\GetInvitationRequest;
use App\Http\Requests\Api\V1\Invitation\GetUserRequest;
use App\Http\Requests\Api\V1\Invitation\InvitationRequest;
use App\Http\Requests\Api\V1\Invitation\PaymentReceiptRequest;
use App\Http\Requests\Api\V1\Invitation\RemoveUserRequest;
use App\Http\Requests\Api\V1\Invitation\SendNotificationToUserRequest;
use App\Http\Requests\Api\V1\Invitation\AddContactRequest;
use App\Http\Requests\Api\V1\Invitation\ShareInvitationRequest;
use App\Http\Requests\Api\V1\Invitation\StoreAdminRequest;
use App\Http\Requests\Api\V1\Invitation\StoreGuardRequest;
use App\Http\Requests\Api\V1\Invitation\StoreUserRequest;
use App\Http\Requests\Api\V1\Invitation\UpdateAdminHostNameRequest;
use App\Http\Requests\Api\V1\Invitation\UpdateAdminInvitationCountRequest;
use App\Http\Requests\Api\V1\Invitation\UpdateStatusRequest;
use App\Http\Requests\Api\V1\Invitation\UpdateUserRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Invitations\InvitationResource;
use App\Http\Resources\Package\PackageResource;
use App\Http\Resources\User\AdminResource;
use App\Http\Resources\User\GuardResource;
use App\Http\Resources\User\UserResource;
use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Invitation;
use App\Models\InvitationContactLog;
use App\Models\InvitationPackage;
use App\Models\Package;
use App\Models\User;
use App\Services\External\Notification;
use App\Jobs\SendBaileysInvitationContactMessage;
use App\Jobs\SendBaileysInvitationMessage;
use App\Services\External\BaileysGateway;
use App\Services\External\BaileysWhatsApp;
use App\Services\External\TwilioSMS;
use App\Services\RespondActive;
use App\Support\PhoneNumber;
use App\Traits\SendsNotificationAndEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class InvitationsController extends Controller
{
    use SendsNotificationAndEmail;

    public function index(GetInvitationRequest $request)
    {
        switch ($request->type) {

            case 1:
                return RespondActive::success('action ran successfully', CategoryResource::collection(
                    Category::whereHas('invitations', function ($query) {
                        $query
                            ->where('paid', Constant::PAID_STATUS['Paid'])
                            ->whereHas('users', function ($query) {
                                $query->where('invitation_user.user_id', auth()->id());
                            });
                    })->with(['invitations' => function ($query) {
                        $query
                            ->where('paid', Constant::PAID_STATUS['Paid'])
                            ->whereHas('users', function ($query) {
                                $query->where('invitation_user.user_id', auth()->id())->latest('invitations.id');
                            });
                    }])
                        ->paginate())->response()->getData());

                break;
            case 2:
                return RespondActive::success('action ran successfully', CategoryResource::collection(

                    Category::whereRelation('invitations', 'user_id', auth()->id())
                        ->with(['invitations' => function ($query) {
                            $query->where('user_id', auth()->id())->latest('id')
                                ->ByStatus([Constant::INVITATION_STATUS['Approved'], Constant::INVITATION_STATUS['Pending user approval']]);
                        }])
                        ->paginate())->response()->getData());
                break;
            case 3:
                return RespondActive::success('action ran successfully',
                    CategoryResource::collection(
                        Category::whereHas('invitations', function ($query) {
                            $query->whereHas('admins', function ($query) {
                                $query->where('invitation_user.user_id', auth()->id());
                            });
                        })
                        ->with(['invitations' => function ($query) {
                            $query->whereHas('admins', function ($query) {
                                $query->where('invitation_user.user_id', auth()->id());
                            })
                            ->orderBy('id', 'desc');
                        }])
                        ->paginate()
                    )->response()->getData());

                break;
        }
    }

    public function store(InvitationRequest $request)
    {
        $invitation = Invitation::query()->create($request->validated()
            + [
                'user_id' => auth()->id(),
                'host_name' => auth()->user()->name,
                'name' => $request->event_name,
                'date' => $request->date,
                'time' => $request->time,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'event_name' => $request->event_name,
                'slug' => slug($request->event_name),
                'host_name' => $request->host_name,

            ]);

        $user = User::findOrFail(auth()->id());

        $data = [];
        switch ($request->invitation_type) {
		// Contact Design , user contact to admin to create invitation
            case Constant::INVITATION_TYPE['Contact Design']:
                if ($request->image) {
                    $imageMime = (string) $request->image->getMimeType();
                    $clientMime = (string) $request->image->getClientMimeType();
                    $imageExt = strtolower((string) ($request->image->getClientOriginalExtension() ?: $request->image->extension()));
                    $originalName = strtolower((string) $request->image->getClientOriginalName());
                    $videoExtensions = ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'avi', 'm4v', 'mkv'];
                    $isVideoUpload = ($imageMime && str_starts_with($imageMime, 'video/'))
                        || ($clientMime && str_starts_with($clientMime, 'video/'))
                        || in_array($imageExt, $videoExtensions, true)
                        || (bool) preg_match('/\.(mp4|webm|ogg|ogv|mov|avi|m4v|mkv)$/', $originalName)
                        || (int) $request->invitation_media_type === Constant::FILE_TYPE['Video'];

                    if ($isVideoUpload) {
                        storeVideo([
                            'value' => $request->image,
                            'file_key' => Constant::FILE_KEY['Not Main'],
                            'file_type' => Constant::FILE_TYPE['Video'],
                            'folderName' => Constant::INVITATION_VIDEO_FOLDER_NAME,
                            'model' => $invitation,
                            'saveInDatabase' => true,
                        ]);
                    } else {
                        storeImage([
                            'value' => $request->image,
                            'folderName' => Constant::INVITATION_IMAGE_FOLDER_NAME,
                            'file_key' => Constant::FILE_KEY['Not Main'],
                            'file_type' => Constant::FILE_TYPE['Image'],
                            'model' => $invitation,
                            'saveInDatabase' => true,
                        ]);
                    }
                }
                if ($request->video) {
                    storeVideo([
                        'value' => $request->video,
                        'file_key' => Constant::FILE_KEY['Not Main'],
                        'file_type' => Constant::FILE_TYPE['Video'],

                        'folderName' => Constant::INVITATION_VIDEO_FOLDER_NAME,
                        'model' => $invitation,
                        'saveInDatabase' => true,
                    ]);
                }
                if ($request->audio) {
                    storeAudio([
                        'value' => $request->audio,
                        'file_key' => Constant::FILE_KEY['Not Main'],
                        'file_type' => Constant::FILE_TYPE['Audio'],
                        'folderName' => Constant::INVITATION_AUDIO_FOLDER_NAME,
                        'model' => $invitation,
                        'saveInDatabase' => true,
                    ]);
                }

                // Send notification and mail to admin when invitation request created
                try {
                    $this->sendAdminNotification(
                        notificationKey: 'invitation_request_created',
                        targetId: $invitation->id,
                        params: [
                            'invitation_id' => $invitation->id,
                            'invitation_name' => $invitation->event_name ?? $invitation->name,
                            'user_name' => $user->name ?? 'User',
                            'user_id' => $user->id,
                            'invitation_type' => 'Contact Design',
                            'status' => 'Pending Admin Approval',

                        ],
                        category: Constant::NOTIFICATION_CATEGORY['Order'] ?? 1,
                        notificationType: Constant::NOTIFICATION_ORDER_TYPES['New Order Created'] ?? 1,
                        emailSubject: 'New Invitation Request - '.($invitation->event_name ?? $invitation->name),
                        emailView: 'emails.order.invitation_request_created',
                         emailTo: env('MAIL_TO_ADDRESS'),
                        emailData: [
                            'invitation' => $invitation,
                            'user' => $user,
                            'invitation_type' => 'Contact Design',
                            'status' => 'Pending Admin Approval',

                        ]
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send payment receipt notification: '.$e->getMessage(), [
                        'invitation_id' => $invitation->id,
                        'error' => $e->getTraceAsString(),
                    ]);
                }
                break;


	  // User Design , user design invitation and send to admin to approve
            case Constant::INVITATION_TYPE['User Design']:

                if ($request->image) {
                    storeImage([
                        'value' => $request->image,
                        'folderName' => Constant::INVITATION_MAIN_IMAGE_FOLDER_NAME,
                        'model' => $invitation,
                        'saveInDatabase' => true,
                        'file_key' => Constant::FILE_KEY['Main'],
                    ]);
                }
                $invitation->update(['status' => Constant::INVITATION_STATUS['Approved']]);
                $data['packages'] = PackageResource::collection(
                    Package::active()
                        ->invitationPackageType(Constant::INVITATION_TYPE['User Design'])
                        ->whereIn('package_type', [
                            Constant::PACKAGE_TYPE['Static Package'],
                            Constant::PACKAGE_TYPE['Free Package'],
                        ])
                        ->excludeUsedFreePackagesForUser(auth()->id())
                        ->get()
                );
                $dynamicPackage = Package::active()
                    ->invitationPackageType(Constant::INVITATION_TYPE['User Design'])
                    ->PackageType(Constant::PACKAGE_TYPE['Dynamic Package'])
                    ->latest()
                    ->first();
                $data['single_invitation_price'] = $dynamicPackage?->price ?? 0;

                // Send notification when invitation request created
                try {
                    $this->sendAdminNotification(
                        notificationKey: 'invitation_created',
                        targetId: $invitation->id,
                        params: [
                            'invitation_id' => $invitation->id,
                            'invitation_name' => $invitation->event_name ?? $invitation->name,
                            'user_name' => $user->name ?? 'User',
                            'user_id' => $user->id,
                            'invitation_type' => 'User Design',
                            'status' => 'Approved',

                        ],
                        category: Constant::NOTIFICATION_CATEGORY['Order'] ?? 1,
                        notificationType: Constant::NOTIFICATION_ORDER_TYPES['New Order Created'] ?? 1,
                        emailSubject: 'New Invitation - '.($invitation->event_name ?? $invitation->name),
                        emailView: 'emails.order.invitation_created',
                        emailTo: env('MAIL_TO_ADDRESS'),
                        emailData: [
                            'invitation' => $invitation,
                            'user' => $user,
                            'invitation_type' => 'User Design',
                            'status' => 'Approved',

                        ]
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send payment receipt notification: '.$e->getMessage(), [
                        'invitation_id' => $invitation->id,
                        'error' => $e->getTraceAsString(),
                    ]);
                }
                break;
            case Constant::INVITATION_TYPE['App Design']:
                break;
        }
        // Mail::send('emails.new-invitation-mail', ['invitation' => $invitation], function ($message) {
        //     $message->to('moderninvitation420@gmail.com', 'دعوة جديدة')
        //         ->from('info@modern-invitation.com', 'Modern Invitation')
        //         ->subject("دعوة جديدة");
        // });

        $data['packages'] = PackageResource::collection(
            Package::active()
                ->invitationPackageType($invitation->invitation_type)
                ->whereIn('package_type', [
                    Constant::PACKAGE_TYPE['Static Package'],
                    Constant::PACKAGE_TYPE['Free Package'],
                ])
                ->excludeUsedFreePackagesForUser(auth()->id())
                ->get(),
        );

        $dynamicPackage = Package::active()
            ->invitationPackageType($invitation->invitation_type)
            ->PackageType(Constant::PACKAGE_TYPE['Dynamic Package'])
            ->latest()
            ->first();
        $data['single_invitation_price'] = $dynamicPackage?->price ?? 0;

        $data['invitation'] = new InvitationResource($invitation);

        return RespondActive::success(__('messages.invitation-created-successfully'), $data);
    }

        public function update(InvitationRequest $request, Invitation $invitation)
        {
            $data = [];

            $user = User::findOrFail(auth()->id());


            switch ($request->invitation_step) {
                case Constant::INVITATION_STEP['Choose Package']:
                    // Get dynamic package price
                    $dynamicPackage = Package::active()
                        ->invitationPackageType($invitation->invitation_type)
                        ->PackageType(Constant::PACKAGE_TYPE['Dynamic Package'])
                        ->latest()
                        ->first();

                    if (! $dynamicPackage || ! $dynamicPackage->price) {
                        return RespondActive::clientError('no extra invitation settings');
                    }

                    $singleInvitationPrice = $dynamicPackage->price;
                    $invitationPackage = null;
                    $user = auth()->user();

                    // Create InvitationPackage if package_id is provided
                    if ($request->package_id) {
                        $invitationPackage = InvitationPackage::query()
                            ->create([
                                'invitation_id' => $invitation->id,
                                'package_id' => $request->package_id,
                                'count' => $request->count ?? 0,
                                'price' => (($request->count ?? 0)) * $singleInvitationPrice,
                                'status' => Constant::PAID_STATUS['Not Paid'],
                            ]);

                        // Send notification when package is chosen
                        // try {
                        //     $this->sendAdminNotification(
                        //         notificationKey: 'package_chosen',
                        //         targetId: $invitation->id,
                        //         params: [
                        //             'invitation_id' => $invitation->id,
                        //             'invitation_name' => $invitation->event_name ?? $invitation->name,
                        //             'user_name' => $user->name ?? 'User',
                        //             'user_id' => $user->id,
                        //             'invitation_type' => $invitation->invitation_type,
                        //             'status' => 'Package Selected',
                        //             'step' => 'Choose Package',
                        //         ],
                        //         category: Constant::NOTIFICATION_CATEGORY['Order'] ?? 1,
                        //         notificationType: Constant::NOTIFICATION_ORDER_TYPES['New Order Created'] ?? 1,
                        //         emailTo: 'moderninvitation420@gmail.com',
                        //         emailSubject: 'Package Chosen - '.($invitation->event_name ?? $invitation->name),
                        //         emailView: 'emails.order.package_chosen',
                        //         emailData: [
                        //             'invitation' => $invitation,
                        //             'user' => $user,
                        //             'invitation_type' => $invitation->invitation_type,
                        //             'status' => 'Package Selected',
                        //             'step' => 'Choose Package',
                        //             'package' => $invitationPackage,
                        //         ]
                        //     );
                        // } catch (\Exception $e) {
                        //     \Illuminate\Support\Facades\Log::error('Failed to send package chosen notification: '.$e->getMessage(), [
                        //         'invitation_id' => $invitation->id,
                        //         'error' => $e->getTraceAsString(),
                        //     ]);
                        // }
                    }

                    // Handle receipt image upload (only if InvitationPackage exists)
                    if ($request->image) {
                        if (! $invitationPackage) {
                            return RespondActive::clientError('Package must be selected before uploading receipt image');
                        }

                        storeImage([
                            'value' => $request->image,
                            'folderName' => Constant::INVITATION_RECEIPT_FOLDER_NAME,
                            'file_key' => Constant::FILE_KEY['Receipt'],
                            'file_type' => Constant::FILE_TYPE['Image'],
                            'model' => $invitationPackage,
                            'saveInDatabase' => true,
                        ]);

                        // Update invitation status to pending payment
                        $invitation->update([
                            'paid' => Constant::PAID_STATUS['Pending Admin Payment'],
                        ]);

                        // Send notification when receipt is uploaded
                        try {
                            $this->sendAdminNotification(
                                notificationKey: 'package_chosen',
                                targetId: $invitation->id,
                                params: [
                                    'invitation_id' => $invitation->id,
                                    'invitation_name' => $invitation->event_name ?? $invitation->name,
                                    'user_name' => $user->name ?? 'User',
                                    'user_id' => $user->id,
                                    'invitation_type' => $invitation->invitation_type,
                                    'status' => 'Pending Admin Approval',
                                    'step' => 'Choose Package',

                                ],
                                category: Constant::NOTIFICATION_CATEGORY['Payment'] ?? 1,
                                notificationType: Constant::NOTIFICATION_PAYMENT_TYPES['New Payment Received'] ?? 1,
                                emailSubject: 'Payment Receipt Uploaded - '.($invitation->event_name ?? $invitation->name),
                                emailView: 'emails.order.package_chosen',
                                emailTo: env('MAIL_TO_ADDRESS'),
                                emailData: [
                                    'invitation' => $invitation,
                                    'user' => $user,
                                    'invitation_type' => $invitation->invitation_type,
                                    'status' => 'Pending Admin Approval',
                                    'step' => 'Choose Package',
                                    'package' => $invitationPackage,

                                ]
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send payment receipt notification: '.$e->getMessage(), [
                                'invitation_id' => $invitation->id,
                                'error' => $e->getTraceAsString(),
                            ]);
                        }
                    }

                    return RespondActive::success('Package chosen successfully',
                        UserResource::collection($invitation->usersByRole(Constant::INVITATION_USER_ROLE['User'])->paginate())->response()->getData());
                case Constant::INVITATION_STEP['Invite Users']:
                    foreach ($request->user_id as $user_id) {
                        $invitation->usersByRole(Constant::INVITATION_USER_ROLE['User'])->sync([
                            $user_id => ['role' => Constant::INVITATION_USER_ROLE['User']],
                        ]);
                    }
                    $data['admins'] = AdminResource::collection($invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])->paginate())
                        ->response()->getData();
                    $data['guards'] = GuardResource::collection($invitation->usersByRole(Constant::INVITATION_USER_ROLE['Guard'])->paginate())
                        ->response()->getData();

                    return RespondActive::success('Logged in successfully', $data);
                case Constant::INVITATION_STEP['Add Admin']:
                    foreach ($request->admin_id as $user_id) {
                        $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])->sync([
                            $user_id => [
                                'role' => Constant::INVITATION_USER_ROLE['Admin'],
                                'invitation_count' => $request->invitation_count,
                            ],
                        ]);
                    }
                    break;
                case Constant::INVITATION_STEP['Add Guard']:
                    if (isset($request->guard_id) && count($request->guard_id) > 0) {
                        foreach ($request->guard_id as $user_id) {
                            $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Guard'])->sync([
                                $user_id => ['role' => Constant::INVITATION_USER_ROLE['Guard']],
                            ]);
                        }
                    }
                    if (isset($request->extra_guard_id) && count($request->extra_guard_id) > 0) {
                        foreach ($request->extra_guard_id as $user_id) {
                            $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Extra Guard'])->sync([
                                $user_id => ['role' => Constant::INVITATION_USER_ROLE['Extra Guard']],
                            ]);
                        }
                    }

                    break;
                case Constant::INVITATION_STEP['Update Invitation']:
                    $invitation->update([
                        'name' => $request->event_name ?? $invitation->event_name,
                        'description' => $request->description ?? $invitation->description,
                        'date' => $request->date ?? $invitation->date,
                        'time' => $request->time ?? $invitation->time,
                        'latitude' => $request->latitude ?? $invitation->latitude,
                        'longitude' => $request->longitude ?? $invitation->longitude,
                        'address' => $request->address ?? $invitation->address,
                        'event_name' => $request->event_name ?? $invitation->event_name,
                        'invitation_media_type' => $request->invitation_media_type ?? $invitation->invitation_media_type,
                        'host_name' => $request->host_name ?? $invitation->host_name,
                        'status' => $request->status ?? $invitation->status,
                    ]);
                    if ($request->image) {
                        storeImage([
                            'value' => $request->image,
                            'folderName' => Constant::INVITATION_MAIN_IMAGE_FOLDER_NAME,
                            'model' => $invitation,
                            'saveInDatabase' => true,
                            'file_key' => Constant::FILE_KEY['Main'],
                        ]);
                    }

                   // if user update invitation status to approved send notification to admin
                   if($request->status == Constant::INVITATION_STATUS['Approved']){

                    //Final Design Delivered
                    try {
                        $this->sendAdminNotification(
                            notificationKey: 'final_design_delivered',
                            targetId: $invitation->id,
                            params: [
                                'invitation_id' => $invitation->id,
                                'invitation_name' => $invitation->event_name ?? $invitation->name,
                                'user_name' => $user->name ?? 'User',
                                'user_id' => $user->id,
                                'invitation_type' => $invitation->invitation_type,
                                'status' => 'Approved',
                                'step' => 'Update Invitation',

                            ],
                            category: Constant::NOTIFICATION_CATEGORY['Order'] ?? 1,
                            notificationType: Constant::NOTIFICATION_ORDER_TYPES['Final Design Delivered'] ?? 1,
                            emailSubject: 'Final Design Delivered - '.($invitation->event_name ?? $invitation->name),
                            emailView: 'emails.order.invitation_modified',
                            emailTo: env('MAIL_TO_ADDRESS'),
                            emailData: [
                                'invitation' => $invitation,
                                'user' => $user,
                                'invitation_type' => $invitation->invitation_type,
                                'status' => 'Approved',
                                'step' => 'Update Invitation',

                            ]
                        );
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send invitation modified notification: '.$e->getMessage(), [
                            'invitation_id' => $invitation->id,
                            'error' => $e->getTraceAsString(),
                        ]);
                    }
                   }else {

                    try {
                        $this->sendAdminNotification(
                            notificationKey: 'invitation_modified',
                            targetId: $invitation->id,
                            params: [
                                'invitation_id' => $invitation->id,
                                'invitation_name' => $invitation->event_name ?? $invitation->name,
                                'user_name' => $user->name ?? 'User',
                                'user_id' => $user->id,
                                'invitation_type' => $invitation->invitation_type,
                                'status' => 'Pending User Approval',
                                'step' => 'Update Invitation',

                            ],
                            category: Constant::NOTIFICATION_CATEGORY['Order'] ?? 1,
                            notificationType: Constant::NOTIFICATION_ORDER_TYPES['Order Modified or Canceled'] ?? 1,
                            emailSubject: 'Invitation Modified - '.($invitation->event_name ?? $invitation->name),
                            emailView: 'emails.order.invitation_modified',
                            emailTo: env('MAIL_TO_ADDRESS'),
                            emailData: [
                                'invitation' => $invitation,
                                'user' => $user,
                                'invitation_type' => $invitation->invitation_type,
                                'status' => 'Pending User Approval',
                                'step' => 'Update Invitation',

                            ]
                        );
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send invitation modified notification: '.$e->getMessage(), [
                            'invitation_id' => $invitation->id,
                            'error' => $e->getTraceAsString(),
                        ]);
                    }
                   }
            }

            return RespondActive::success('Invitation modified successfully', [
                'invitation' => $invitation,
            ], 200);
        }




    public function show(Invitation $invitation)
    {
        $adminInvitationCount = $invitation->admins()->where('user_id', auth()->id())->first();

        $data = new InvitationResource($invitation->load(['category', 'user']));
        $data['admin_invitation_count'] = ($adminInvitationCount?->pivot?->invitation_count) - ((int) $invitation->users()->where('invited_by', auth()->id())->sum('invitation_count') ?? 0);

        if (auth()->user()->id != $invitation->user_id) {
            $data['host_name'] = $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])->where('user_id', auth()->user()->id)->first()?->pivot?->host_name;
        }

        return  RespondActive::success('action ran successfully', $data);
    }

    /**
     * Get invitation details by ID instead of route model binding.
     */
    public function showById(int $id)
    {
        $invitation = Invitation::findOrFail($id);

        return $this->show($invitation);
    }

    // delete method
public function delete($id){

$invitation = Invitation::findOrFail($id);
$invitation->delete();

        return RespondActive::success('Invitation Deleted Successfully',[]);

}

    public function users(GetUserRequest $request, Invitation $invitation)
    {
        $usersInvitationCount = 0;

        $invitedUsers = $invitation->users()->where('invited_by', auth()->id())->get();

        foreach ($invitedUsers as $user) {
            $usersInvitationCount += $user->pivot->invitation_count;
        }
        $data = [];
        $extraCountInvitation = InvitationPackage::query()
                                            ->where('invitation_id', '=', $invitation->id)
                                            ->where('status', '=', 1)
                                            ->get()->sum('count') ?? 0;

        $data['users_count'] = $usersInvitationCount;
        $data['users_delivered'] = (int) $invitation->users()->whereIn('seen', [Constant::SEEN_STATUS['delivered'], Constant::SEEN_STATUS['accepted'], Constant::SEEN_STATUS['seen'], Constant::SEEN_STATUS['scanned']])->where('invited_by', auth()->id())->sum('invitation_count');
        $data['users_not_delivered'] = (int) $invitation->users()->wherePivot('seen', Constant::SEEN_STATUS['in app'])->where('invited_by', auth()->id())->sum('invitation_count');
        $data['users_not_downloaded_app'] = (int) $invitation->users()->wherePivot('seen', Constant::SEEN_STATUS['not in the app'])->where('invited_by', auth()->id())->sum('invitation_count');
        $data['users_not_attended'] = (int) $invitation->users()->wherePivot('seen', Constant::SEEN_STATUS['all not attended'])->where('invited_by', auth()->id())->sum('invitation_count');
        $invitationCount = checkPackageCount($invitation, 'checkAllUsersInvitationCount');
        $data['users_rest_of_package'] = ($extraCountInvitation + $invitationCount) - $usersInvitationCount;
        $usersQuery = $invitation->users()
            ->when($request->seen > 1 && $request->seen != null, function ($query) use ($request) {
                if ($request->seen == 2) {
                    $query->whereIn('invitation_user.seen', [Constant::SEEN_STATUS['accepted'], Constant::SEEN_STATUS['seen'], Constant::SEEN_STATUS['scanned']]);
                } elseif ($request->seen == 5) {
                    $query->whereIn('invitation_user.seen', [Constant::SEEN_STATUS['declined'], Constant::SEEN_STATUS['all not attended']]);
                } elseif ($request->seen == 8) {
                    $query->whereNotIn('invitation_user.seen', [Constant::SEEN_STATUS['scanned']]);
                } else {
                    $query->where('invitation_user.seen', $request->seen);
                }
            })
            ->when($request->seen <= 1 && $request->seen != null, function ($query) {
                $query->whereIn('invitation_user.seen', [Constant::SEEN_STATUS['not in the app'], Constant::SEEN_STATUS['in app']]);
            })
            ->where('invitation_user.invited_by', auth()->id());

        $usersModels = $usersQuery->get();

        $invitation->loadMissing(['category']);

        $appleLink = env('APPLE_LINK');
        $googlePlayLink = env('GOOGLE_PLAY_LINK');

        // Ensure invitation_link, invitation_message, and store links are present for this endpoint.
        // UserResource reads dynamic attributes; Eloquent users won't have them unless we attach them.
        foreach ($usersModels as $u) {
            $u->invitation_link = route('user.invitation.show', [
                'invitation_code' => $invitation->code,
                'user_id' => $u->id,
                'inserted_by' => auth()->id(),
            ]);
            $u->invitation_message = $this->buildInvitationMessage($invitation, $u->id);
            $u->apple_link = $appleLink;
            $u->google_play_link = $googlePlayLink;
        }

        $users = UserResource::collection($usersModels);

        $data['users'] = $users;

        return RespondActive::success('action ran successfully', $data);
    }

    public function guards(Invitation $invitation)
    {
        $users = GuardResource::collection(
            $invitation->guards()->get());

        return RespondActive::success('action ran successfully', $users);
    }

    public function admins(Invitation $invitation)
    {
//        dd($invitation->admins()->get()->pluck('invitedToUsers'));

        $data['sum_count'] = (int) $invitation->admins()->sum('invitation_count');
        $data['rest_count'] = $invitation->admins->pluck('invitedToUsers')->flatten()->count();
        $data['admins'] = AdminResource::collection($invitation->admins()->get());

        return RespondActive::success('action ran successfully', $data);
    }

    public function removeUser(RemoveUserRequest $request, Invitation $invitation)
    {
        $invitation->usersByRole($request->role)->detach($request->user_id);

        return RespondActive::success('Action ran successfully');
    }

    public function addUser(StoreUserRequest $request, Invitation $invitation)
    {
        if ($invitation->user_id == auth()->id()) {
            if (count($request->users) > $invitation->totalUnPaidInvitationsCount()) {
                return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
            }

            if (! checkPackageCount($invitation, 'checkAllInvitationPackagesCount', $request->totalInvitationCount())) {
                return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
            }
        } else {
            if (! checkPackageCountForAdmin($invitation, $request->totalInvitationCount(), auth()->id())) {
                return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
            }
        }

        $invitationCountForEveryUser = 0;
        foreach ($request->users as $userArray) {
            $invitationCountForEveryUser += $userArray['invitation_count'];
            if ($invitation->totalInvitationsCount() < $invitationCountForEveryUser) {
                return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
            }

            $phone = checkCountryCode(str_replace(' ', '', $userArray['phone']));
            $user = User::where(
                [
                    'phone' => $phone['phone'],
                    'country_code' => $phone['country_code'],
                ])->first();
            $seen = 1;
            if (! $user) {
                $user = User::create(
                    [
                        'phone' => str_replace(' ', '', $phone['phone']),
                        'country_code' => $phone['country_code'],
                        'register_type' => Constant::REGISTER_TYPE['Added By User'],
                        'name' => $userArray['name'],
                    ]);
                $seen = 0;
            }
            $invitation->usersByRole(Constant::INVITATION_USER_ROLE['User'])->sync([
                $user->id => [
                    'role' => Constant::INVITATION_USER_ROLE['User'],
                    'invitation_count' => $userArray['invitation_count'],
                    'invited_by' => auth()->id(),
                    'seen' => $seen,
                    'name' => $userArray['name'],
                ],
            ], false);
            $image = QrCode::format('png')
                ->size(200)
                ->color(0, 0, 0)
                ->backgroundColor(255, 255, 255, 0)
                ->style('square')
                ->generate($invitation->id.'-'.$user->id);
            $output_file = 'public/qr-code/Qr-'.$invitation->id.'-'.$user->id.'.png';
            Storage::disk('local')->put($output_file, $image);
            if ($invitation->paid == Constant::PAID_STATUS['Paid']) {
                Notification::notify('users',
                    Constant::NOTIFICATIONS_TYPE['Invitations'],
                    [$user->id],
                    $invitation->id,
                    'invitation_received');
            }
            $user['invitation_link'] = route('user.invitation.show', ['invitation_code' => $invitation->code, 'user_id' => $user->id, 'inserted_by' => auth()->id()]);
        }

        return RespondActive::success('Action ran successfully', (UserResource::collection($invitation->users)));
    }

    public function addContact(AddContactRequest $request, Invitation $invitation)
    {
        $contacts = $request->input('contacts', []);

        if ($error = $this->ensureContactInvitationQuota($invitation, count($contacts))) {
            return $error;
        }

        $result = $this->persistInvitationContacts($invitation, $contacts);

        if (count($result['stored']) === 0) {
            return RespondActive::clientError(__('messages.invitation_contacts_all_invalid'), [
                'skipped' => $result['skipped'],
            ]);
        }

        return RespondActive::success(__('messages.invitation_contacts_stored'), [
            'stored_count' => count($result['stored']),
            'skipped_count' => count($result['skipped']),
            'contacts' => collect($result['stored'])
                ->map(fn (array $entry) => $this->formatContactLog($entry['log']))
                ->values()
                ->all(),
            'skipped' => $result['skipped'],
        ]);
    }

    public function updateAdminHostName(UpdateAdminHostNameRequest $request, Invitation $invitation)
    {
        $admin = $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])->where('user_id', auth()->id())->first();

        if (! $admin) {
            return RespondActive::clientError(__('validation.admin_not_found_in_invitation'));
        }

        // Update the pivot row for the authenticated admin on this invitation
        $invitation
            ->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])
            ->updateExistingPivot(auth()->id(), ['host_name' => $request->host_name]);

        return RespondActive::success('Action ran successfully');
    }

    public function editUser(UpdateUserRequest $request, User $user)
    {
        $invitation = Invitation::whereId($request->invitation_id)->first();

        if ($invitation->user_id == auth()->id()) {
            if (! checkPackageCountForUser($invitation, $request->invitation_count, $user->id)) {
                return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
            }
        } else {
            if (! checkPackageCountForAdmin($invitation, $request->invitation_count, auth()->id(), 'update', $user->id)) {
                return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
            }
        }

        $invitation->users()
            ->sync([
                $user->id => [
                    'invitation_count' => $request->invitation_count,
                    'name' => $request->name ?? $user->name,
                ],
            ], false);

        $phone = checkCountryCode(str_replace(' ', '', $request->phone));

        $user->update(['phone' => $phone['phone'], 'country_code' => $phone['country_code']]);
        if ($invitation->paid == Constant::PAID_STATUS['Paid']) {
            Notification::notify('users',
                Constant::NOTIFICATIONS_TYPE['Invitations'],
                [$user->id],
                $invitation->id,
                'invitation_received');
        }

        return RespondActive::success('Action ran successfully', (new UserResource($user)));
    }

    public function addAdmin(StoreAdminRequest $request, Invitation $invitation)
    {
        // Authorization check: Only invitation owner can add admins
        if ($invitation->user_id !== auth()->id()) {
            return RespondActive::clientError(__('validation.unauthorized_to_add_admin'), [], 403);
        }

        // Parse phone number with country code
        $phone = checkCountryCode(str_replace(' ', '', $request->phone));

        // Check if user exists
        $user = User::where([
            'phone' => $phone['phone'],
            'country_code' => $phone['country_code'],
        ])->first();

        if (! $user) {
            return RespondActive::clientError(__('validation.user_doesnot_exist'));
        }

        if ($invitation->user_id == $user->id) {
            return RespondActive::clientError(__('validation.you_cannot_add_yourself_as_an_admin'));
        }

        if ($invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])->where('user_id', $user->id)->exists()) {
            return RespondActive::clientError(__('validation.user_already_admin_for_invitation'));
        }

        // Check if user is already an admin for this invitation
        $existingAdmin = $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])
            ->where('user_id', $user->id)
            ->first();

        if ($existingAdmin) {
            return RespondActive::clientError(__('validation.user_already_admin_for_invitation'));
        }

        // Validate package count limits
        if (! checkPackageCount($invitation, 'checkAdminInvitationCount', $request->invitation_count)) {
            return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
        }

        // Wrap in database transaction for consistency
        DB::beginTransaction();

        try {
            // Add user as admin
            $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])->sync([
                $user->id => [
                    'role' => Constant::INVITATION_USER_ROLE['Admin'],
                    'invitation_count' => $request->invitation_count,
                    'name' => $request->name ?? $user->name,
                    'host_name' => $request->host_name ?? $user->name,
                ],
            ], false);

            // Send notification after successful database operation
            Notification::notify('users',
                Constant::NOTIFICATIONS_TYPE['Updated Invitations'],
                [$user->id],
                $invitation->id,
                'admin_added');

            DB::commit();

            // Prepare response data
            $user['admin_name'] = $request->name ?? $user->name;
            $user['invitation_count'] = $request->invitation_count;

            return RespondActive::success('Action ran successfully', new AdminResource($user));
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error adding admin to invitation: '.$e->getMessage());

            return RespondActive::serverError(__('validation.failed_to_add_admin'));
        }
    }

    /**
     * Update the invitation count for a specific admin in an invitation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAdminInvitationCount(UpdateAdminInvitationCountRequest $request, Invitation $invitation, User $admin)
    {
        // Check if the admin exists in this invitation with admin role
        $adminExists = $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])
            ->where('user_id', $admin->id)
            ->exists();

        if (! $adminExists) {
            return RespondActive::clientError(__('validation.admin_not_found_in_invitation'));
        }

        // Check if the user is authorized to update this invitation
        if ($invitation->user_id !== auth()->id() && ! $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])->where('user_id', auth()->id())->exists()) {
            return RespondActive::clientError(__('validation.unauthorized_to_update_invitation'));
        }

        // Validate the new invitation count against package limits
        if (! checkPackageCount($invitation, 'checkAdminInvitationCount', $request->invitation_count)) {
            return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
        }

        // Update the admin's invitation count in the pivot table
        $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])
            ->updateExistingPivot($admin->id, [
                'invitation_count' => $request->invitation_count,
            ]);

        // Get the updated admin data
        $updatedAdmin = $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])
            ->where('user_id', $admin->id)
            ->first();

        // Prepare response data
        $admin['admin_name'] = $updatedAdmin->pivot->name;
        $admin['invitation_count'] = $request->invitation_count;

        // Send notification to the admin about the update
        Notification::notify('users',
            Constant::NOTIFICATIONS_TYPE['Updated Invitations'],
            [$admin->id],
            $invitation->id,
            'admin_invitation_count_updated',
            ['count' => $request->invitation_count]);

        return RespondActive::success(__('validation.invitation_count_updated_successfully'), new AdminResource($admin));
    }

    public function addGuard(StoreGuardRequest $request, Invitation $invitation)
    {
        // $phoneWithCountryCode = phoneByIpLocation($request->phone);
        if (count($invitation->guards) >= 2) {
            return RespondActive::clientError('You exceeded number of available guards please pay first!');
        }
        $phone = checkCountryCode(str_replace(' ', '', $request->phone));

        $guard = User::where(
            [
                'phone' => $phone['phone'],
                'country_code' => $phone['country_code'],
            ])->first();

        if (! $guard) {
            $guard = User::create(
                [
                    'phone' => str_replace(' ', '', $phone['phone']),
                    'country_code' => $phone['country_code'],
                    'password' => '',
                    'name' => $request->name,
                    'register_type' => Constant::REGISTER_TYPE['Added By User'],
                ]);
        }

        if (! $request->extra) {
            $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Guard'])->sync([
                $guard->id => [
                    'role' => Constant::INVITATION_USER_ROLE['Guard'],
                    'name' => $request->name,
                    'password' => bcrypt($request->password),

                ],
            ], false);
        } else {
            $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Extra Guard'])->sync([
                $guard->id => [
                    'role' => Constant::INVITATION_USER_ROLE['Extra Guard'],
                    'name' => $request->name,
                    'password' => bcrypt($request->password),

                ],
            ], false);
        }

        Notification::notify('users',
            Constant::NOTIFICATIONS_TYPE['Invitations'],
            [$guard->id],
            $invitation->id,
            'guard_added');

        return RespondActive::success('Action ran successfully', new GuardResource($invitation->guards()->where('user_id', $guard->id)->first()));
    }

    /**
     * Build dynamic message template for invitation notifications
     *
     * @param  string  $templateType
     * @return string
     */
    private function buildInvitationMessage(Invitation $invitation, $user_id, $templateType = 'invitation_notification_template')
    {
        $eventType = $invitation->category ? $invitation->category->name : $invitation->event_name;
        $hostName = $invitation->host_name;

        if ((int) $invitation->user_id !== (int) auth()->id()) {
            // Look up the admin who invited the user and read host_name from pivot
            $admin = $invitation->usersByRole(Constant::INVITATION_USER_ROLE['Admin'])
                ->wherePivot('user_id', auth()->id())
                ->first();

            if ($admin && isset($admin->pivot) && ! empty($admin->pivot->host_name)) {
                $hostName = $admin->pivot->host_name;
            }
        }

        $invitationLink = route('user.invitation.show', [
            'invitation_code' => $invitation->code,
            'user_id' => $user_id,
            'inserted_by' => auth()->id(),
        ]);

        return __('messages.'.$templateType, [
            'event_type' => $eventType,
            'host_name' => $hostName,
            'invitation_link' => $invitationLink,
            'apple_link' => env('APPLE_LINK'),
            'google_play_link' => env('GOOGLE_PLAY_LINK'),
        ]);
    }

    private function buildStyledInvitationMessage(Invitation $invitation, $messageBody, $templateType = 'styled_invitation_template')
    {
        $eventType = $invitation->category ? $invitation->category->name : $invitation->event_name;
        $hostName = auth()->user()->id == $invitation->user_id ? $invitation->host_name : $invitation->user->name;

        return __('messages.'.$templateType, [
            'event_type' => $eventType,
            'host_name' => $hostName,
            'message_body' => $messageBody,
            'apple_link' => env('APPLE_LINK'),
            'google_play_link' => env('GOOGLE_PLAY_LINK'),
        ]);
    }

    public function sendNotificationToUser(SendNotificationToUserRequest $request, Invitation $invitation)
    {
        if ($error = $this->ensureClientWhatsAppConnected()) {
            return $error;
        }

        if ($invitation->users()->count() > 0) {
            $this->queueInvitationMessages(
                $invitation,
                $invitation->users()->where('invited_by', auth()->id())->get(),
                fn ($user) => $this->buildStyledInvitationMessage($invitation, $request->message)
            );

            return RespondActive::success(__('messages.whatsapp_invitations_queued'));
        }

        return RespondActive::clientError('no_users_found_for_this_invitation');
    }

    public function sendSMSToUser(SendNotificationToUserRequest $request, Invitation $invitation)
    {
        if ($error = $this->ensureClientWhatsAppConnected()) {
            return $error;
        }

        $this->queueInvitationMessages(
            $invitation,
            $invitation->users,
            function ($user) use ($request, $invitation) {
                $message = $request->use_template || ! $request->message
                    ? $this->buildInvitationMessage($invitation, 'invitation_sms_template')
                    : $request->message;

                return str_replace('{user_id}', $user->id, $message);
            }
        );

        Notification::notify('users',
            Constant::NOTIFICATIONS_TYPE['Invitations'],
            $invitation->users()->pluck('users.id')->toArray(),
            $invitation->id,
            'invitation_notification');

        return RespondActive::success(__('messages.whatsapp_invitations_queued'));
    }

    /**
     * Send template message to all users in the invitation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTemplateMessage(Invitation $invitation)
    {
        if ($error = $this->ensureClientWhatsAppConnected()) {
            return $error;
        }

        if ($invitation->users()->count() > 0) {
            $this->queueInvitationMessages(
                $invitation,
                $invitation->users,
                function ($user) use ($invitation) {
                    $smsMessage = $this->buildInvitationMessage($invitation, 'invitation_sms_template');

                    return str_replace('{user_id}', $user->id, $smsMessage);
                }
            );

            Notification::notify('users',
                Constant::NOTIFICATIONS_TYPE['Invitations'],
                $invitation->users()->pluck('users.id')->toArray(),
                $invitation->id,
                'invitation_notification');

            return RespondActive::success(__('messages.whatsapp_invitations_queued'));
        }

        return RespondActive::clientError('no_users_found_for_this_invitation');
    }

    public function updateStatus(UpdateStatusRequest $request, Invitation $invitation)
    {
        $invitation->update($request->validated());

          $user = User::findOrFail(auth()->id());
	if ($request->status == Constant::INVITATION_STATUS['Approved']) {
	 //Final Design Delivered
                    try {
                        $this->sendAdminNotification(
                            notificationKey: 'final_design_delivered',
                            targetId: $invitation->id,
                            params: [
                                'invitation_id' => $invitation->id,
                                'invitation_name' => $invitation->event_name ?? $invitation->name,
                                'user_name' => $user->name ?? 'User',
                                'user_id' => $user->id,
                                'invitation_type' => $invitation->invitation_type,
                                'status' => 'Approved',
                                'step' => 'Update Invitation',

                            ],
                            category: Constant::NOTIFICATION_CATEGORY['Order'] ?? 1,
                            notificationType: Constant::NOTIFICATION_ORDER_TYPES['Final Design Delivered'] ?? 1,
                            emailSubject: 'Final Design Delivered - '.($invitation->event_name ?? $invitation->name),
                            emailView: 'emails.order.invitation_modified',
                            emailTo: env('MAIL_TO_ADDRESS'),
                            emailData: [
                                'invitation' => $invitation,
                                'user' => $user,
                                'invitation_type' => $invitation->invitation_type,
                                'status' => 'Approved',
                                'step' => 'Update Invitation',

                            ]
                        );
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send invitation modified notification: '.$e->getMessage(), [
                            'invitation_id' => $invitation->id,
                            'error' => $e->getTraceAsString(),
                        ]);
                    }

	}


        if ($invitation->paid == Constant::PAID_STATUS['Paid'] && $request->status == Constant::INVITATION_STATUS['Cancelled']) {
            foreach ($invitation->users as $user) {
                Notification::notify('users',
                    Constant::NOTIFICATIONS_TYPE['Invitations'],
                    [$user->id],
                    $invitation->id,
                    'invitation_cancelled');
            }
        }

        return RespondActive::success('Action ran successfully');
    }

    public function checkInvitation(CheckInvitationRequest $request)
    {
        $invitation = Invitation::where('id', $request->invitation_id)->first();

        //Invitation include to this guard

        if (! $invitation->guards()->where('user_id', auth()->id())->exists()) {
            return RespondActive::clientError(__('messages.sorry_user_not_invited'));
        }

        if ($invitation) {
            // First check if user exists in this invitation at all
            $userInInvitation = $invitation->users()->where('user_id', $request->user_id)->first();

            if (! $userInInvitation) {
                // User doesn't exist in this invitation
                return RespondActive::clientError(__('messages.sorry_user_not_invited'), ['status' => false, 'message' => __('messages.sorry_user_not_invited'), 'invitation_count' => 0]);
            }

            // Check if user is already scanned
            if ($userInInvitation->pivot->seen == Constant::SEEN_STATUS['scanned']) {
                // User is already scanned
                return RespondActive::success(__('messages.already_scanned'), ['status' => false, 'message' => __('messages.already_scanned'), 'invitation_count' => 0]);
            }

            // User exists and is not scanned yet - mark as scanned
            $invitation->users()->where('user_id', $request->user_id)->update(['seen' => Constant::SEEN_STATUS['scanned']]);

            return RespondActive::success('Action ran successfully', ['status' => true, 'message' => __('messages.user_scanned_successfully'), 'invitation_count' => $userInInvitation->pivot->invitation_count, 'guest_name' => $userInInvitation->pivot->name, 'guest_phone' => $userInInvitation->country_code.$userInInvitation->phone]);
        } else {
            return RespondActive::clientError(__('messages.sorry_user_not_invited'), ['status' => false, 'message' => __('messages.sorry_user_not_invited'), 'invitation_count' => 0]);
        }
    }

public function PaymentReceipt(PaymentReceiptRequest $request, Invitation $invitation)
{
    DB::beginTransaction();
    $invitationPackage = InvitationPackage::query()
        ->where('invitation_id', '=', $invitation->id)
          //   ->where('status', '=', Constant::PAID_STATUS['Not Paid'])
        ->first();

	//     if (! $invitationPackage) {
	//         return RespondActive::clientError('Sorry, there are existing unpaid invitation packages.');
	//     }

    $invitationPackage->update([
        'status' => Constant::PAID_STATUS['Pending Admin Payment'],
    ]);

    $invitation->update([
        'paid' => Constant::PAID_STATUS['Pending Admin Payment'],
    ]);

    storeImage(['value' => $request->image,
        'folderName' => Constant::INVITATION_RECEIPT_FOLDER_NAME,
        'file_key' => Constant::FILE_KEY['Receipt'],
        'file_type' => Constant::FILE_TYPE['Image'],
        'model' => $invitationPackage,
        'saveInDatabase' => true]);
    Mail::send('emails.payment-receipt', ['invitationPackage' => $invitationPackage, 'invitation' => $invitation], function ($message) {
        $message->to('moderninvitation420@gmail.com', 'دفع باقة جديدة')
                ->from('info@modern-invitation.com', 'Modern Invitation')
                ->subject('دفع باقة جديدة');
    });

    $user = User::findOrFail(auth()->id());

    // send notification to admin
	try {
		$this->sendAdminNotification(
		notificationKey: 'payment_receipt_uploaded',
		targetId: $invitation->id,
		params: [
			'invitation_id' => $invitation->id,
			'invitation_name' => $invitation->event_name ?? $invitation->name,
			'user_name' => $user->name ?? 'User',
			'user_id' => $user->id,
			'invitation_type' => $invitation->invitation_type,
			'status' => 'Pending Admin Approval',
			'step' => 'Choose Package',

		],
		category: Constant::NOTIFICATION_CATEGORY['Order'] ?? 1,
		notificationType: Constant::NOTIFICATION_ORDER_TYPES['Order Modified or Canceled'] ?? 1,
		emailSubject: 'Payment Receipt Uploaded - '.($invitation->event_name ?? $invitation->name),
		emailView: 'emails.order.payment_receipt_uploaded',
		emailTo: env('MAIL_TO_ADDRESS'),
		emailData: [
		'invitation' => $invitation,
		'user' => $user,
		'invitation_type' => $invitation->invitation_type,
		'status' => 'Pending Admin Approval',
		'step' => 'Choose Package',
		'package' => $invitationPackage,
		]
	);
	} catch (\Exception $e) {
		DB::rollBack();
		Log::error('Failed to send extra packages added notification: '.$e->getMessage(), [
		'invitation_id' => $invitation->id,
		'error' => $e->getTraceAsString(),
		]);
	}


    DB::commit();

    return RespondActive::success(__('action ran successfully'));
}

    public function shareInvitation(ShareInvitationRequest $request, Invitation $invitation)
    {
        if ($error = $this->ensureClientWhatsAppConnected()) {
            return $error;
        }

        $contacts = $request->input('contacts', []);

        if (is_array($contacts) && count($contacts) > 0) {
            return $this->shareInvitationToContacts($invitation, $contacts);
        }

        $users = $invitation->users()
            ->whereIn('seen', [Constant::SEEN_STATUS['in app'], Constant::SEEN_STATUS['not in the app']])
            ->where('invited_by', auth()->id())
            ->get();

        $this->queueInvitationMessages(
            $invitation,
            $users,
            fn ($user) => $this->buildInvitationMessage($invitation, $user->id, 'invitation_sms_template'),
            true
        );

        return RespondActive::success(__('messages.whatsapp_invitations_queued'));
    }

    public function contactInvitationLogs(Invitation $invitation)
    {
        $logs = InvitationContactLog::query()
            ->where('invitation_id', $invitation->id)
            ->where('invited_by', auth()->id())
            ->latest()
            ->get()
            ->map(fn (InvitationContactLog $log) => $this->formatContactLog($log));

        return RespondActive::success('OK', [
            'logs' => $logs,
        ]);
    }

    public function shareSmsInvitationApp(Invitation $invitation, User $user)
    {
        $message = $this->buildInvitationMessage($invitation, $user->id, 'invitation_sms_template');

        return RespondActive::success('Action ran successfully', $message);
    }

        public function shareInvitationSms(Invitation $invitation)
        {
            $sentCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($invitation->users()->where('seen', Constant::SEEN_STATUS['Sent'])->where('invited_by', auth()->id())->get() as $user) {
                // Build dynamic message template for this user
                $message = $this->buildInvitationMessage($invitation, $user->id, 'invitation_sms_template');

                // $response = UltraMessage::send(
            //     $user->country_code . $user->phone,
            //     $message,
            //     '',
            //     $invitation->id . '-' . $user->id
                // );

                $response = TwilioSMS::sendWithTemplate([
                    'phone' => $user->phone,
                    'country_code' => $user->country_code,
                    'message' => $message,
                ]);

                if (isset($response->sent) && $response->sent == 'true') {
                    $invitation->users()->where('user_id', $user->id)->update(['seen' => Constant::SEEN_STATUS['delivered']]);
                    $sentCount++;
                } else {
                    $failedCount++;
                    $errors[] = [
                        'user_id' => $user->id,
                        'phone' => $user->phone,
                        'error' => $response->error ?? 'Unknown error',
                    ];
                }
            }

            return RespondActive::success('SMS sent successfully', [
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
            ]);
        }

       public function completeRequestInvitation(Invitation $invitation)
       {
           $invitationPackages = InvitationPackage::query()
               ->select([
                   'invitation_package.id',
                   'packages.price',
                   'packages.count',
                   'packages.free_invitations_count',
                   'invitation_package.count as extra_count',
                   'invitation_package.price as extra_price',
               ])
               ->where('invitation_package.invitation_id', $invitation->id)
               ->whereIn('invitation_package.status', [Constant::PAID_STATUS['Not Paid'], Constant::PAID_STATUS['Pending Admin Payment'], Constant::PAID_STATUS['Rejected'] ])
               ->join('packages', 'packages.id', '=', 'invitation_package.package_id')
               ->first();

           if (! $invitationPackages) {
               return RespondActive::clientError(__('No unpaid package found for this invitation'));
           }

           $appSetting = AppSetting::query()
               ->where('key', 'account_number')
               ->first();

           $data = [
               'invitation' => $invitation,
               'package' => [
                   'id' => $invitationPackages->id,
                   'count' => $invitationPackages->count,
                   'price' => $invitationPackages->price,
                   'free_invitations_count' => $invitationPackages->free_invitations_count,
               ],
               'extra' => [
                   'count' => $invitationPackages->extra_count,
                   'price' => $invitationPackages->extra_price ?? 0,
               ],
               'account_number' => $appSetting?->value ?? '',
               'total_price' => $invitationPackages->price + ($invitationPackages->extra_price ?? 0),
           ];

           return RespondActive::success('Action ran successfully', $data);
       }

         public function addExtraPackages(Request $request, Invitation $invitation)
         {
             // Check if there are existing unpaid packages
             $unpaidPackages = InvitationPackage::query()
                              // ->where('status', Constant::PAID_STATUS['Not Paid'])
                                   ->where('invitation_id', $invitation->id)
                                   ->get();

          //    if ($unpaidPackages->isNotEmpty() && count($unpaidPackages) > 0) {
          //        return RespondActive::clientError('Sorry, there are existing unpaid invitation packages.');
          //    }

             $singleInvitationPrice = Package::active()
             ->invitationPackageType($invitation->invitation_type)
             ->PackageType(Constant::PACKAGE_TYPE['Dynamic Package'])
             ->latest()
             ->first()->price;

             if (! $singleInvitationPrice) {
                 return RespondActive::clientError('no extra invitation settings');
             }

             $packageId = $request->input('package_id');
             $count = $request->input('count');

             if (empty($packageId) && empty($count)) {
                 return RespondActive::clientError('Both package ID and count are required.');
             }
             try {
                 DB::beginTransaction();

                 $invitationPackage = InvitationPackage::query()->create([
                     'invitation_id' => $invitation->id,
                     'package_id' => $packageId,
                     'count' => $count,
                     'price' => $count * $singleInvitationPrice,
                     'status' => Constant::PAID_STATUS['Pending Admin Payment'],
                 ]);

                 // Handle image upload if provided
                 if ($request->hasFile('image')) {
                     storeImage([
                         'value' => $request->file('image'),
                         'folderName' => Constant::INVITATION_RECEIPT_FOLDER_NAME,
                         'file_key' => Constant::FILE_KEY['Receipt'],
                         'file_type' => Constant::FILE_TYPE['Image'],
                         'model' => $invitationPackage,
                         'saveInDatabase' => true,
                     ]);
                 }

                 $invitation->update([
                     'paid' => Constant::PAID_STATUS['Pending Admin Payment'],
                 ]);

	// send notification to admin
	try {
		$this->sendAdminNotification(
		notificationKey: 'extra_packages_added',
		targetId: $invitation->id,
		params: [
			'invitation_id' => $invitation->id,
			'invitation_name' => $invitation->event_name ?? $invitation->name,
			'user_name' => auth()->user()->name ?? 'User',
			'user_id' => auth()->id(),
			'invitation_type' => $invitation->invitation_type,
			'status' => 'Pending Admin Payment',
			'step' => 'Add Extra Packages',

		],
		category: Constant::NOTIFICATION_CATEGORY['Order'] ?? 1,
		notificationType: Constant::NOTIFICATION_ORDER_TYPES['Order Modified or Canceled'] ?? 1,
		emailSubject: 'Extra Packages Added - '.($invitation->event_name ?? $invitation->name),
		emailView: 'emails.order.extra_packages_added',
		emailTo: env('MAIL_TO_ADDRESS'),
		emailData: [
		'invitation' => $invitation,
		'user' => auth()->user(),
		'invitation_type' => $invitation->invitation_type,
		'status' => 'Pending Admin Payment',
		'step' => 'Add Extra Packages',
		'package' => $invitationPackage,
		]
	);
	} catch (\Exception $e) {
		DB::rollBack();
		Log::error('Failed to send extra packages added notification: '.$e->getMessage(), [
		'invitation_id' => $invitation->id,
		'error' => $e->getTraceAsString(),
		]);
	}


          //        // Send email notification
          //        Mail::send('emails.payment-receipt',
          //            [
          //                'invitationPackage' => $invitationPackage,
          //                'invitation' => $invitation,
          //            ], function ($message) {
          //                $message->to('moderninvitation420@gmail.com')
          //                    ->from('info@modern-invitation.com', 'Modern Invitation')
          //           ->subject('دفع باقة جديدة');
          //            });

                 DB::commit();

                 // Return updated list of unpaid packages
                 $updatedUnpaidPackages = InvitationPackage::query()
                     ->where('status', 3)
                     ->where('invitation_id', $invitation->id)
                     ->get();

                 return RespondActive::success('Action ran successfully', $updatedUnpaidPackages);
             } catch (\Exception $e) {
                 DB::rollBack();
                 \Log::error('Error adding extra packages: '.$e->getMessage());

                 return RespondActive::serverError('An error occurred while processing your request.');
             }
         }

    private function clientSessionId(): string
    {
        return BaileysGateway::sessionIdForUser((int) auth()->id());
    }

    private function ensureClientWhatsAppConnected(): ?\Illuminate\Http\JsonResponse
    {
        if (! BaileysGateway::isConfigured()) {
            return RespondActive::clientError(__('messages.whatsapp_gateway_not_configured'));
        }

        $status = BaileysGateway::getStatus($this->clientSessionId());
        $connectionStatus = $status['data']['status'] ?? 'disconnected';

        if (! $status['ok'] || $connectionStatus !== 'connected') {
            return RespondActive::clientError(__('messages.whatsapp_not_connected'));
        }

        return null;
    }

    /**
     * @param  array<int, array{name?: string, phone?: string}>  $contacts
     */
    private function shareInvitationToContacts(Invitation $invitation, array $contacts)
    {
        if ($error = $this->ensureContactInvitationQuota($invitation, count($contacts))) {
            return $error;
        }

        $persisted = $this->persistInvitationContacts($invitation, $contacts);
        $hostId = (int) auth()->id();
        $queued = [];
        $index = 0;

        foreach ($persisted['stored'] as $entry) {
            $log = $entry['log'];
            $guestUser = $entry['user'];
            $contact = $entry['contact'];
            $referenceId = $log->reference_id ?? $invitation->id.'-contact-'.$guestUser->id.'-'.$log->id;

            $message = $this->buildInvitationMessage($invitation, $guestUser->id, 'invitation_sms_template');
            $dayOffset = intdiv($index, 80);
            $delaySeconds = ($index % 80) * 12;

            SendBaileysInvitationContactMessage::dispatch(
                contactLogId: $log->id,
                hostUserId: $hostId,
                invitationId: $invitation->id,
                guestUserId: $guestUser->id,
                countryCode: $contact['country_code'],
                phone: $contact['phone'],
                message: $message,
                referenceId: $referenceId,
            )->delay(
                now()->addDays($dayOffset)->setHour(10)->addSeconds($delaySeconds)
            );

            $queued[] = $this->formatContactLog($log->fresh());
            $index++;
        }

        if ($index === 0 && count($persisted['skipped']) > 0) {
            return RespondActive::clientError(__('messages.invitation_contacts_all_invalid'), [
                'skipped' => $persisted['skipped'],
            ]);
        }

        return RespondActive::success(__('messages.whatsapp_contact_invitations_queued'), [
            'queued_count' => count($queued),
            'skipped_count' => count($persisted['skipped']),
            'queued' => $queued,
            'skipped' => $persisted['skipped'],
        ]);
    }

    /**
     * @param  array<int, array{name?: string, phone?: string}>  $contacts
     * @return array{stored: list<array{log: InvitationContactLog, user: User, contact: array}>, skipped: list<array<string, string>>}
     */
    private function persistInvitationContacts(Invitation $invitation, array $contacts): array
    {
        $hostId = (int) auth()->id();
        $defaultCountryCode = (string) auth()->user()->country_code;
        $stored = [];
        $skipped = [];

        foreach ($this->normalizeShareContacts($contacts, $defaultCountryCode) as $contact) {
            if (! $contact['valid']) {
                $skipped[] = [
                    'name' => $contact['name'],
                    'phone' => $contact['phone'],
                    'reason' => __('messages.invitation_contact_invalid_phone'),
                ];

                continue;
            }

            $guestUser = $this->resolveContactGuestUser($invitation, $contact);

            $log = InvitationContactLog::query()->updateOrCreate(
                [
                    'invitation_id' => $invitation->id,
                    'invited_by' => $hostId,
                    'country_code' => $contact['country_code'],
                    'phone' => $contact['phone'],
                ],
                [
                    'user_id' => $guestUser->id,
                    'contact_name' => $contact['name'],
                    'send_status' => Constant::INVITATION_CONTACT_SEND_STATUS['pending'],
                    'seen' => Constant::SEEN_STATUS['not in the app'],
                    'reference_id' => $invitation->id.'-contact-'.$guestUser->id,
                    'error_message' => null,
                    'sent_at' => null,
                ]
            );

            $stored[] = [
                'log' => $log,
                'user' => $guestUser,
                'contact' => $contact,
            ];
        }

        return [
            'stored' => $stored,
            'skipped' => $skipped,
        ];
    }

    private function ensureContactInvitationQuota(Invitation $invitation, int $contactCount)
    {
        if ($contactCount <= 0) {
            return RespondActive::clientError(__('messages.invitation_contacts_required'));
        }

        if ($invitation->user_id == auth()->id()) {
            if ($contactCount > $invitation->totalUnPaidInvitationsCount()) {
                return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
            }

            if (! checkPackageCount($invitation, 'checkAllInvitationPackagesCount', $contactCount)) {
                return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
            }
        } elseif (! checkPackageCountForAdmin($invitation, $contactCount, auth()->id())) {
            return RespondActive::clientError(__('validation.exceeded_number_of_invited_users'));
        }

        return null;
    }

    /**
     * @param  array<int, array{name?: string, phone?: string}>  $contacts
     * @return list<array{name: string, phone: string, country_code: string, e164: string, valid: bool}>
     */
    private function normalizeShareContacts(array $contacts, string $defaultCountryCode): array
    {
        $normalized = [];
        $seenPhones = [];

        foreach ($contacts as $contact) {
            $name = trim((string) ($contact['name'] ?? ''));
            $rawPhone = trim((string) ($contact['phone'] ?? ''));

            if ($rawPhone === '') {
                continue;
            }

            $e164 = PhoneNumber::e164ForWhatsAppPairing($defaultCountryCode, $rawPhone);
            if (isset($seenPhones[$e164])) {
                continue;
            }
            $seenPhones[$e164] = true;

            $countryCode = $defaultCountryCode;
            $ccDigits = preg_replace('/\D+/', '', $defaultCountryCode);
            $phoneForDb = PhoneNumber::informationForStorage($countryCode, $rawPhone);

            $normalized[] = [
                'name' => $name !== '' ? $name : $phoneForDb,
                'phone' => $phoneForDb,
                'country_code' => $countryCode,
                'e164' => $e164,
                'valid' => PhoneNumber::isValidWhatsAppPairingNumber($e164, $countryCode),
            ];
        }

        return $normalized;
    }

    /**
     * @param  array{name: string, phone: string, country_code: string}  $contact
     */
    private function resolveContactGuestUser(Invitation $invitation, array $contact): User
    {
        $user = User::query()
            ->where('phone', $contact['phone'])
            ->where('country_code', $contact['country_code'])
            ->first();

        if (! $user) {
            $user = User::query()->create([
                'phone' => $contact['phone'],
                'country_code' => $contact['country_code'],
                'register_type' => Constant::REGISTER_TYPE['Added By User'],
                'name' => $contact['name'],
            ]);
        }

        $pivotExists = $invitation->users()
            ->where('users.id', $user->id)
            ->exists();

        if (! $pivotExists) {
            $invitation->usersByRole(Constant::INVITATION_USER_ROLE['User'])->sync([
                $user->id => [
                    'role' => Constant::INVITATION_USER_ROLE['User'],
                    'invitation_count' => 1,
                    'invited_by' => auth()->id(),
                    'seen' => Constant::SEEN_STATUS['not in the app'],
                    'name' => $contact['name'],
                ],
            ], false);

            $image = QrCode::format('png')
                ->size(200)
                ->color(0, 0, 0)
                ->backgroundColor(255, 255, 255, 0)
                ->style('square')
                ->generate($invitation->id.'-'.$user->id);
            $outputFile = 'public/qr-code/Qr-'.$invitation->id.'-'.$user->id.'.png';
            Storage::disk('local')->put($outputFile, $image);
        }

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function formatContactLog(InvitationContactLog $log): array
    {
        $sendStatus = match ((int) $log->send_status) {
            Constant::INVITATION_CONTACT_SEND_STATUS['sent'] => 'sent',
            Constant::INVITATION_CONTACT_SEND_STATUS['failed'] => 'failed',
            default => 'pending',
        };

        return [
            'id' => $log->id,
            'contact_name' => $log->contact_name,
            'phone' => $log->phone,
            'country_code' => $log->country_code,
            'user_id' => $log->user_id,
            'send_status' => $sendStatus,
            'seen' => (int) $log->seen,
            'seen_label' => array_search((int) $log->seen, Constant::SEEN_STATUS, true) ?: 'unknown',
            'error_message' => $log->error_message,
            'sent_at' => $log->sent_at?->toIso8601String(),
            'created_at' => $log->created_at?->toIso8601String(),
        ];
    }

    /**
     * @param  iterable<int, User>  $users
     */
    private function queueInvitationMessages(
        Invitation $invitation,
        iterable $users,
        callable $messageBuilder,
        bool $withReferenceId = false
    ): void {
        $hostId = (int) auth()->id();
        $index = 0;

        foreach ($users as $user) {
            $message = $messageBuilder($user);
            $dayOffset = intdiv($index, 80);
            $delaySeconds = ($index % 80) * 12;

            SendBaileysInvitationMessage::dispatch(
                hostUserId: $hostId,
                invitationId: $invitation->id,
                guestUserId: $user->id,
                countryCode: (string) $user->country_code,
                phone: (string) $user->phone,
                message: $message,
                referenceId: $withReferenceId ? $invitation->id.'-'.$user->id : '',
            )->delay(
                now()->addDays($dayOffset)->setHour(10)->addSeconds($delaySeconds)
            );

            $index++;
        }
    }
}
