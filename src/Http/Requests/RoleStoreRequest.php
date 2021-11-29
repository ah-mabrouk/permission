<?php

namespace Mabrouk\RolePermissionGroup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mabrouk\RolePermissionGroup\Models\Role;

class RoleStoreRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:191|unique:role_translations,name',
            'description' => 'nullable|string|max:10000',
        ];
    }

    public function storeRole()
    {
        $this->role = Role::create([]);
        return $this->role->refresh();
    }
}
