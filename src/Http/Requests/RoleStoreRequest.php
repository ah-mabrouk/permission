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
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:191|unique:role_translations,name',
            'description' => 'nullable|string|max:10000',
        ];
    }

    public function storeRole(): Role
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
