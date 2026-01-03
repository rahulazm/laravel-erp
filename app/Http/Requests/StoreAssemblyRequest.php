<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssemblyRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }
    
    public function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:assemblies',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:main,sub,component',
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