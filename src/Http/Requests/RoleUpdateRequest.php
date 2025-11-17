<?php

namespace Mabrouk\Permission\Http\Requests;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Mabrouk\Permission\Models\Role;
use Mabrouk\Translatable\Rules\UniqueForLocale;

class RoleUpdateRequest extends FormRequest
{
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
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:191',
                new UniqueForLocale(request()->role),
            ],
            'description' => 'nullable|string|max:10000',
            'permissions' => 'sometimes|array',
            'permissions.*.id' => 'required_with:permissions|integer|distinct|exists:permissions,id',
            'permissions.*.sub_permissions' => 'nullable|array',
            'permissions.*.sub_permissions.*' => 'nullable|integer|distinct|exists:sub_permissions,id',
        ];
    }

    public function getValidatorInstance()
    {
        $this->merge(['locale' => request()->input('locale')]);
        return parent::getValidatorInstance();
    }

    public function updateRole(): Role
    {
        $currentTranslationNamespace = config('translatable.translation_models_path');
        config(['translatable.translation_models_path' => 'Mabrouk\Permission\Models']);
        DB::transaction(function () {
            $this->role->update([]);
            $this->updatePermissions();
        });
        config(['translatable.translation_models_path' => $currentTranslationNamespace]);

        return $this->role->refresh();
    }

    public function updatePermissions(): self
    {
        if ($this->exists('permissions') && $this->role->id != 0) {
            $permissions = [];
            for ($i = 0; $i < \count($this->permissions); $i++) {
                if (\in_array('id', \array_keys($this->permissions[$i])) && \in_array('sub_permissions', \array_keys($this->permissions[$i]))) {
                    $permissions[$this->permissions[$i]['id']] = \in_array('sub_permissions', \array_keys($this->permissions[$i])) ?  $this->permissions[$i]['sub_permissions'] : [];
                }
            }
            $this->role->syncPermissions($permissions);
        }

        return $this;
    }

    public function attributes(): array
    {
        return [
            'name' => __('mabrouk/permission/roles.attributes.name'),
            'description' => __('mabrouk/permission/roles.attributes.description'),
            'permissions' => __('mabrouk/permission/roles.attributes.permissions'),
            'permissions.*.id' => __('mabrouk/permission/roles.attributes.permission'),
            'permissions.*.sub_permissions' => __('mabrouk/permission/roles.attributes.sub_permissions'),
            'permissions.*.sub_permissions.*' => __('mabrouk/permission/roles.attributes.sub_permission'),
        ];
    }
}
