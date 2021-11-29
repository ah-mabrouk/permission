<?php

namespace Mabrouk\Permission\Http\Resources;

use Mabrouk\Permission\Models\PermissionGroup;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        request()->permissionsIds = request()->permissionsIds ?? $this->permissionsIds;
        request()->subPermissionsIds = request()->subPermissionsIds ?? $this->subPermissionsIds;
        return [
            'id' => $this->id,
            'name' => $this->name,
            // 'description' => $this->description,
            'permission_groups' => PermissionGroupResource::collection(PermissionGroup::all()),
        ];
    }
}
