<?php

namespace Mabrouk\RolePermissionGroup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|min:3|max:191|unique:role_translations,name,' . translation_id($this->role),
            // 'description' => 'nullable|string|max:10000',
            'permissions' => 'sometimes|array',
            'permissions.*.id' => 'required|integer|distinct|exists:permissions,id',
            'permissions.*.sub_permissions' => 'nullable|array',
            'permissions.*.sub_permissions.*' => 'nullable|integer|distinct|exists:sub_permissions,id',
        ];
    }

    public function updateRole()
    {
        $this->role->update([
            'id' => $this->role->id,
        ]);
        $this->updatePermissions();
        return $this->role->refresh();
    }

    public function updatePermissions()
    {
        if ($this->exists('permissions') && $this->role->id != 0) {
            $permissions = [];
            for ($i = 0; $i < \count($this->permissions); $i++) {
                $permissions[$this->permissions[$i]['id']] = $this->permissions[$i]['sub_permissions'];
            }
            $this->role->syncPermissions($permissions);
        }
        return $this;
    }
}
