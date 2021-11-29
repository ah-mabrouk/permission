<?php

namespace Mabrouk\Permission\Http\Requests;

use Illuminate\Support\Facades\DB;
use Mabrouk\Permission\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;

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
        $currentTranslationNamespace = config('translatable.translation_models_path');
        config(['translatable.translation_models_path' => 'Mabrouk\Permission\Models']);
        DB::transaction(function () {
            if ($this->exists('name')) {
                $this->permission_group->update([
                    'id' => $this->permission_group->id,
                ]);
            }
            $this->updateGroupPermissions();
        });
        config(['translatable.translation_models_path' => $currentTranslationNamespace]);
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
