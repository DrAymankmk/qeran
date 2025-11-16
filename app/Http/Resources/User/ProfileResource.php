<?php

namespace App\Http\Resources\User;

use App\Helpers\Constant;
use App\Models\Invitation;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected $seen;
    protected $invitation_count;
    protected $new_name;

    public function seen($seen,$invitation_count,$name){
        $this->seen = $seen;
        $this->invitation_count = $invitation_count;
        $this->new_name = $name;
        return $this;
    }

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'verified'      => $this->verified,
            'email_verified'      => $this->email_verified_at?Constant::VERIFICATION_STATUS['Verified']:Constant::VERIFICATION_STATUS['Not verified'],
            'email'         => $this->email,
            'phone'         =>$this->phone,
            'country_code'  => $this->country_code,
            'image'         => $this->image(),
            $this->mergeWhen(auth('sanctum')->user() && auth('sanctum')->id()==$this->id,function (){
                return [
                    'description'=>$this->description,
                    'gender'=>$this->gender
                ];

            }),
            'role' => $this->whenPivotLoaded('invitation_user',
                function () {
                    return $this->pivot->role;
                }),
//            'invitation_count' => $this->whenPivotLoaded('invitation_user',
//                function () {
//                    return $this->pivot->invitation_count;
//                }),
            'name' => $this->pivot?$this->pivot->name:($this->new_name??$this->name),
            'seen' => $this->pivot?$this->pivot->seen:$this->seen,
            'invitation_count' => $this->pivot?$this->pivot->invitation_count:$this->invitation_count,
            'invitation_link'=>$this->pivot? route('user.invitation.show',['slug'=>Invitation::whereId($this->pivot->invitation_id)->first()->slug,'user_id'=>$this->id]):$this->invitation_link,

            'token'         => $this->token,
            'event_name'         => $this->event_name,


        ];
    }
}
