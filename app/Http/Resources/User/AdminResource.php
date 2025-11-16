<?php

namespace App\Http\Resources\User;

use App\Helpers\Constant;
use App\Http\Resources\Category\CategoryResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
//        dd($this->invitedToUsers()->get());
        //$this->pivot->invitation_count-$this->invitedToUsers()->where('invitation_user.invitation_id',$this->pivot->invitation_id)->count()
        return [
            'id' => $this->id,
            'phone' => $this->country_code.$this->phone,
            'country_code' => $this->country_code,
            'name' => $this->admin_name??$this->name,
            'invitation_count' => $this->pivot?$this->pivot->invitation_count:$request->invitation_count,
            'invitation_rest_count' => $this->whenPivotLoaded('invitation_user',
                function () {
                    return $this->invitedToUsers()->where('invitation_user.invitation_id',$this->pivot->invitation_id)->count();
                }),
            'role' => $this->whenPivotLoaded('invitation_user',
                function () {
                    return $this->pivot->role;
                }),

        ];
    }
}
