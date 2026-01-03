<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }
    
    public function rules()
    {
        return [
            'item_code' => 'required|string|max:50|unique:items',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:raw_material,sub_assembly,finished_good',
            'unit_of_measure' => 'required|string|max:20',
            'unit_cost' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'material_grade' => 'nullable|string|max:50',
            'specifications' => 'nullable|array',
            'specifications.key.*' => 'sometimes|string|max:50',
            'specifications.value.*' => 'sometimes|string|max:100',
            'is_obsolete' => 'sometimes|boolean'
        ];
    }
}