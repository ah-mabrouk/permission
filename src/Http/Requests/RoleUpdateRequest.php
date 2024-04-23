<?php

namespace Mabrouk\Permission\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:191',
                Rule::unique('role_translations', 'name')->where(function ($query) {
                    return $query->where('role_translations.role_id', '!=', request()->role->id);
                }),
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

    public function updateRole()
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

    public function updatePermissions()
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
}
