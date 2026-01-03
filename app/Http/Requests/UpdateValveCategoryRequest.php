<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateValveCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }
    
    public function rules()
    {
        return [
            'code' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('valve_categories')->ignore($this->route('valve_category'))
            ],
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'pressure_rating' => 'nullable|string|max:50',
            'temperature_rating' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'end_connection' => 'nullable|string|max:100'
        ];
    }
}