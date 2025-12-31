<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id'       => 'required|exists:stores,id',
            'invoice_code'     => 'required|string|max:50|unique:orders,invoice_code',
            'order_date'     => 'required|date',
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'taker_employee_id' => 'required|exists:employees,id',
            'discount'       => 'nullable|numeric|min:0',

            // Items array validation
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',

            // Attributes inside each item
            'items.*.attributes' => 'nullable|array',
            'items.*.attributes.*' => 'nullable|string|max:255',

            // Documents inside each item (file uploads)
            'items.*.documents' => 'nullable|array',
            'items.*.documents.*' => 'nullable|file|max:2048'
        ];
    }
}
