<?php

namespace Mabrouk\Permission\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mabrouk\Translatable\Rules\UniqueForLocale;

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
                new UniqueForLocale(request()->permission, 'display_name'),
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
