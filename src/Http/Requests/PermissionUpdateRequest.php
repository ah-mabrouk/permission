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
        $this->localeAttribute = $this->locale ?? request()->locale ?? config('app.fallback_locale');

        return [
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:191',
                Rule::unique('permission_translations', 'display_name')->where(function ($query) {
                    return $query->where('locale', $this->localeAttribute)
                        ->where('permission_translations.id', '!=', translation_id(request()->permission));
                }),
            ],
            'description' => 'nullable|string|max:10000',
        ];
    }

    public function getValidatorInstance()
    {
        request()->merge(['display_name' => $this->name ?? $this->permission->name]);
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
