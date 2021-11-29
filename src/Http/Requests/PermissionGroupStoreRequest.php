<?php

namespace Mabrouk\RolePermissionGroup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mabrouk\RolePermissionGroup\Models\PermissionGroup;

class PermissionGroupStoreRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:191|unique:permission_group_translations,name',
        ];
    }

    public function storePermissionGroup()
    {
        $this->permissionGroup = PermissionGroup::create([]);
        return $this->permissionGroup->refresh();
    }
}
