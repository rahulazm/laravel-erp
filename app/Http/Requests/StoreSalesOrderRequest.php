<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesOrderRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }
    
    public function rules()
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'valve_type' => 'required|string|max:100',
            'valve_category_id' => 'required|exists:valve_categories,id',
            'total_amount' => 'required|numeric|min:0',
            'delivery_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:draft,pending,confirmed,in_production,completed,cancelled',
            'create_bom' => 'sometimes|boolean',
            'items' => 'sometimes|array',
            'items.*.item_code' => 'required_with:items|string|max:50',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.unit_of_measure' => 'required_with:items|string|max:20',
            'items.*.notes' => 'nullable|string|max:500'
        ];
    }
    
    public function messages()
    {
        return [
            'delivery_date.after' => 'Delivery date must be in the future.',
            'valve_category_id.exists' => 'Selected valve category does not exist.',
            'items.*.item_code.required_with' => 'Item code is required for all items.',
            'items.*.quantity.integer' => 'Quantity must be a whole number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.'
        ];
    }
}