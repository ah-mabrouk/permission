<?php

namespace Mabrouk\Permission\Http\Requests;

use Mabrouk\Permission\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
{
    public Role $role;

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
        $currentTranslationNamespace = config('translatable.translation_models_path');
        config(['translatable.translation_models_path' => 'Mabrouk\Permission\Models']);
        $this->role = Role::create([]);
        config(['translatable.translation_models_path' => $currentTranslationNamespace]);
        return $this->role->refresh();
    }

    public function attributes(): array
    {
        return [
            'name' => __('mabrouk/permission/roles.attributes.name'),
            'description' => __('mabrouk/permission/roles.attributes.description'),
        ];
    }
}
