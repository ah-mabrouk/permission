<?php

namespace Mabrouk\Permission\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mabrouk\Permission\Models\PermissionGroup;

class PermissionGroupStoreRequest extends FormRequest
{
    public PermissionGroup $permissionGroup;

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
            'name' => 'required|string|min:2|max:191|unique:permission_group_translations,name',
        ];
    }

    public function storePermissionGroup(): PermissionGroup
    {
        $currentTranslationNamespace = config('translatable.translation_models_path');
        config(['translatable.translation_models_path' => 'Mabrouk\Permission\Models']);
        $this->permissionGroup = PermissionGroup::create([]);
        config(['translatable.translation_models_path' => $currentTranslationNamespace]);

        return $this->permissionGroup->refresh();
    }

    public function attributes(): array
    {
        return [
            'name' => __('mabrouk/permission/permission_groups.attributes.name'),
        ];
    }
}
