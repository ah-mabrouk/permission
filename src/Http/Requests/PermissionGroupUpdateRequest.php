<?php

namespace Mabrouk\Permission\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Mabrouk\Permission\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Mabrouk\Permission\Models\PermissionGroup;
use Mabrouk\Translatable\Rules\UniqueForLocale;

class PermissionGroupUpdateRequest extends FormRequest
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
                'min:2',
                'max:191',
                new UniqueForLocale(request()->permission_group),
            ],
            'permissions' => 'sometimes|array',
            'permissions.*' => 'required|exists:permissions,id',
        ];
    }

    public function getValidatorInstance()
    {
        request()->locale = request()->input('locale');
        return parent::getValidatorInstance();
    }

    public function updatePermissionGroup(): PermissionGroup
    {
        $currentTranslationNamespace = config('translatable.translation_models_path');
        config(['translatable.translation_models_path' => 'Mabrouk\Permission\Models']);
        DB::transaction(function () {
            if ($this->exists('name')) {
                $this->permission_group->update([]);
            }
            $this->updateGroupPermissions();
        });
        config(['translatable.translation_models_path' => $currentTranslationNamespace]);

        return $this->permission_group->refresh();
    }

    private function updateGroupPermissions(): self
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

    public function attributes(): array
    {
        return [
            'name' => __('mabrouk/permission/permission_groups.attributes.name'),
            'permissions' => __('mabrouk/permission/permission_groups.attributes.permissions'),
            'permissions.*' => __('mabrouk/permission/permission_groups.attributes.permission'),
        ];
    }
}
