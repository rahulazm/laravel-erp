<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreValveCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }
    
    public function rules()
    {
        return [
            'code' => 'required|string|max:20|unique:valve_categories',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'pressure_rating' => 'nullable|string|max:50',
            'temperature_rating' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'end_connection' => 'nullable|string|max:100'
        ];
    }
}