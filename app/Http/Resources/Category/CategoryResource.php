<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\Advertisement\AdvertisementResource;
use App\Http\Resources\Invitations\InvitationResource;
use App\Services\Website\CountryService;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CategoryResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'name' => $this->name,
            'image' => $this->image(),
            'is_wedding' => $this->is_wedding,
            'is_party' => $this->is_party,
            $this->mergeWhen($this->whenLoaded('invitations'),function (){
                return ['invitations'=>InvitationResource::collection($this->whenLoaded('invitations'))];
            })
        ];
    }
}
