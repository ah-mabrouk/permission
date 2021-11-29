<?php

namespace Mabrouk\RolePermissionGroup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mabrouk\RolePermissionGroup\Models\Permission;

class PermissionGroupUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|min:2|max:191|unique:permission_group_translations,name,' . translation_id($this->permission_group),
            'permissions' => 'sometimes|array',
            'permissions.*' => 'required|exists:permissions,id',
        ];
    }

    public function updatePermissionGroup()
    {
        if ($this->exists('name')) {
            $this->permission_group->update([
                'id' => $this->permission_group->id,
            ]);
        }
        $this->updateGroupPermissions();
        return $this->permission_group->refresh();
    }

    private function updateGroupPermissions()
    {
        if ($this->exists('permissions')) {
            Permission::whereIn('id', $this->permissions)
                ->where('permission_group_id', '!=', $this->permission_group->id)
                ->update([
                    'permission_group_id' => $this->permission_group->id,
                ]);
        }
        return $this;
    }
}
