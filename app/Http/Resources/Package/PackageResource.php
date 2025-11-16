<?php

namespace App\Http\Resources\Package;

use App\Helpers\Constant;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
             $this->mergeWhen(optional($this)->package_type == Constant::PACKAGE_TYPE['Static Package'],function (){
                return [
                    'id'        => optional($this)->id,
                    'count'   => optional($this)->count,
                    'free_invitations_count'   => optional($this)->free_invitations_count,
                ];
            }),
            'price'     => optional($this)->price,
            'package_type'          =>optional($this)->package_type


        ];
    }
}
