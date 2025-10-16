<?php

namespace Mabrouk\Permission\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubPermissionResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->customDisplayName,
            'selected' => $this->isSelected,
        ];
    }
}
