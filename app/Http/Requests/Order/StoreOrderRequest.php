<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_id'       => 'required|string|max:50|unique:orders,invoice_id',
            'order_date'       => 'required|date',
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'total_cost'       => 'required|numeric|min:0',
            'phone_model'      => 'required|string|max:255',
            'moderator_id'     => 'required|exists:moderators,id',
        ];
    }
}
