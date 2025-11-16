<?php

namespace App\Http\Resources\User;

use App\Helpers\Constant;
use App\Http\Resources\Category\CategoryResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class GuardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'phone'         => $this->country_code.$this->phone,
            'country_code'  => $this->country_code,
            'name'  => $this->pivot?$this->pivot->name:$this->name,
            'role' => $this->whenPivotLoaded('invitation_user',
                function () {
                    return $this->pivot->role;
                }),

        ];
    }
}
