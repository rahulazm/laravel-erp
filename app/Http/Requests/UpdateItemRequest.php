<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }
    
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'sometimes|in:raw_material,sub_assembly,finished_good',
            'unit_of_measure' => 'sometimes|string|max:20',
            'unit_cost' => 'sometimes|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'material_grade' => 'nullable|string|max:50',
            'specifications' => 'nullable|array',
            'specifications.key.*' => 'sometimes|string|max:50',
            'specifications.value.*' => 'sometimes|string|max:100',
            'changes_description' => 'required|string|max:500'
        ];
    }
}