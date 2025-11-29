<?php

namespace App\Http\Requests\CourierInvoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourierPaidInvoiceRequest extends FormRequest
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
            'courier_name'     => ['required', Rule::in(array_values(config('constants.couriers')))],
            'consignment_id'   => 'required|string|max:100|unique:courier_paid_invoices,consignment_id',
            'created_date'     => 'nullable|date',
            'invoice_type'     => ['required', Rule::in(array_values(config('constants.paid_invoice_types')))],

            'collected_amount' => 'nullable|numeric|min:0',
            'recipient_name'   => 'nullable|string|max:255',
            'recipient_phone'  => 'nullable|string|max:20',

            'collectable_amount' => 'nullable|numeric|min:0',
            'cod_fee'            => 'nullable|numeric|min:0',
            'delivery_fee'       => 'nullable|numeric|min:0',
            'final_fee'          => 'nullable|numeric|min:0',

            'discount'           => 'nullable|numeric|min:0',
            'additional_charge'  => 'nullable|numeric|min:0',
            'compensation_cost'  => 'nullable|numeric|min:0',
            'promo_discount'     => 'nullable|numeric|min:0',
            'payout'             => 'nullable|numeric|min:0',

            'merchant_order_id' => 'nullable|string|max:255|unique:courier_paid_invoices,merchant_order_id',
        ];
    }
}
