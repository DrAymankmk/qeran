<?php

namespace App\Http\Resources\Notifications;

use App\Helpers\Constant;
use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'id'          => $this->id,
            'title'          => $this->title,
            'description'          => $this->description,
            'type'   => $this->type,
            'target_id'   => $this->target_id,
	  'read_at'   => $this->read_at,
            'created_at'    => $this->created_at,
            'image'    => $this->image(),

        ];
    }
}
