<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssemblyRequest extends FormRequest
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
                'max:50',
                Rule::unique('assemblies')->ignore($this->route('assembly'))
            ],
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'sometimes|in:main,sub,component',
            'parent_id' => 'nullable|exists:assemblies,id',
            'drawing_number' => 'nullable|string|max:100',
            'revision' => 'nullable|string|max:10',
            'components' => 'sometimes|array',
            'components.*.type' => 'required|in:item,assembly',
            'components.*.id' => 'required|integer',
            'components.*.quantity' => 'required|numeric|min:0.001',
            'components.*.uom' => 'required_if:components.*.type,item|string|max:20',
            'components.*.notes' => 'nullable|string|max:500'
        ];
    }
}