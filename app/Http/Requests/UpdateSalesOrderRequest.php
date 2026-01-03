<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesOrderRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }
    
    public function rules()
    {
        return [
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'customer_phone' => 'sometimes|string|max:20',
            'shipping_address' => 'sometimes|string|max:500',
            'valve_type' => 'sometimes|string|max:100',
            'valve_category_id' => 'sometimes|exists:valve_categories,id',
            'total_amount' => 'sometimes|numeric|min:0',
            'delivery_date' => 'sometimes|date',
            'notes' => 'nullable|string|max:1000'
        ];
    }
}