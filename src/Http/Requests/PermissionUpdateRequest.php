<?php

namespace Mabrouk\Permission\Http\Requests;

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
        // if ($this->exists('name') && $this->name != null) {
        //     request()->display_name = $this->name;
        // }
        return [
            'name' => 'sometimes|string|min:3|max:191',
            'description' => 'nullable|string|max:10000',
        ];
    }

    public function getValidatorInstance()
    {
        if ($this->exists('name')) {
            $this->merge(['display_name' => $this->name]);
        }
        return parent::getValidatorInstance();
    }

    public function updatePermission()
    {
        $currentTranslationNamespace = config('translatable.translation_models_path');
        config(['translatable.translation_models_path' => 'Mabrouk\Permission\Models']);
        $this->permission->update([]);
        config(['translatable.translation_models_path' => $currentTranslationNamespace]);
        return $this->permission->refresh();
    }
}
