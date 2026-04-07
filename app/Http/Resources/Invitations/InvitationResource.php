<?php

namespace App\Http\Resources\Invitations;

use App\Helpers\Constant;
use App\Http\Resources\Advertisement\AdvertisementResource;
use App\Models\Admin;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\User\UserResource;
use App\Services\Website\CountryService;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class InvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'category'=>new CategoryResource($this->whenLoaded('category')),
            'host'=>new UserResource($this->whenLoaded('host')),
            'host_name'=> $this->host_name ?? 'غير معروف',
            'image'=>$this->image(),
            'mime_type'=>$this->imageMimeType(),
            'name'=>$this->name ?? 'غير معروف',
            'code'=>$this->code,
            'description'=>$this->description ?? 'غير معروف',
            'latitude'=>(double)$this->latitude??null,
            'longitude'=>(double)$this->longitude??null,
            'address'=>$this->address??null,
            'paid'=>$this->paid??'غير مدفوع',
            'date'=>$this->date?Carbon::parse($this->date)->format('Y-m-d'):null,
            'time'=>$this->time?Carbon::parse($this->time)->format('H:i'):null,
            'day'=>date('l', strtotime("$this->date $this->time")),
            'invitation_step'=>(int)$this->invitation_step,
            'invitation_type'=>(int)$this->invitation_type,
            'status'=> $this->getInvitationStatus(),
            'qr' => $this->qr($this->id),
            'number_of_invitees'=>$this->users()->where('invitation_user.user_id',auth()->id())->first()?->pivot->invitation_count,
            'invitation_count'=>$this->package?->count,
            'invitation_price'=>$this->package?->price,
            'extra_invitation_count'=>$this->count,
            'extra_invitation_price'=>$this->price,
            'admin_invitation_count'=>$this->admin_invitation_count,

            // Hub files split by uploader; recent first in each list
            'media' => $this->formatInvitationMediaByCreator(),


            // 'invitation_count'=> $this->whenPivotLoaded('invitation_user',
            //    function () {
            //        return $this->pivot->invitation_count;
            //    }),

        ];
    }

    /**
     * @return array{admin: \Illuminate\Support\Collection, user: \Illuminate\Support\Collection}
     */
    protected function formatInvitationMediaByCreator(): array
    {
        $mapFile = function ($file) {
            return [
                'id' => $file->id,
                'file_type' => (int) $file->file_type,
                'file_key' => (int) $file->file_key,
                'url' => $file->get_path(),
                'original_name' => $file->original_name,
                'mime_type' => $file->getMimeType,
                'size' => $file->size,
                'created_at' => $file->created_at,
                'created_by_type' => $file->created_by_type,
            ];
        };

        $files = $this->hubFiles ?? collect();

        $adminFiles = $files->filter(function ($file) {
            return $file->created_by_type === Admin::class;
        })->sortByDesc(function ($file) {
            return $file->created_at?->timestamp ?? 0;
        })->values()->map($mapFile);

        // App user uploads, legacy rows without created_by, or any non-admin creator
        $userFiles = $files->filter(function ($file) {
            return $file->created_by_type !== Admin::class;
        })->sortByDesc(function ($file) {
            return $file->created_at?->timestamp ?? 0;
        })->values()->map($mapFile);

        return [
            'admin' => $adminFiles,
            'user' => $userFiles,
        ];
    }
}
