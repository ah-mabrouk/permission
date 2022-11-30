<?php

namespace Mabrouk\Permission\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PermissionUpdateRequest extends FormRequest
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
                Rule::unique('permission_translations', 'display_name')->where(function ($query) {
                    return $query->where('permission_translations.permission_id', '!=', request()->permission->id);
                }),
            ],
            'description' => 'nullable|string|max:10000',
        ];
    }

    public function getValidatorInstance()
    {
        request()->locale = request()->input('locale');
        request()->merge([
            'display_name' => $this->name ?? $this->permission->name,
            'description' => $this->description,
        ]);
        return parent::getValidatorInstance();
    }

    public function updatePermission()
    {
        $currentTranslationNamespace = config('translatable.translation_models_path');
        config(['translatable.translation_models_path' => 'Mabrouk\Permission\Models']);
        request()->permission->update([]);
        config(['translatable.translation_models_path' => $currentTranslationNamespace]);
        return request()->permission->refresh();
    }
}
