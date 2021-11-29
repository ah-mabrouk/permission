<?php

namespace Mabrouk\RolePermissionGroup\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'type' => $this->type,
            'path' => $this->type != 'video' ? url($this->path) : $this->path,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'main' => $this->is_main,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
