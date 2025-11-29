<?php

namespace App\Imports;

use App\Models\CourierPaidInvoice;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class CourierPaidInvoiceImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // ----------------------------
        // 1. Parse created date
        // ----------------------------
        $createdDate = $row['created_date'] ?? null;

        if ($createdDate) {
            if (is_numeric($createdDate)) {
                $createdDate = Carbon::instance(
                    ExcelDate::excelToDateTimeObject($createdDate)
                )->format('Y-m-d H:i:s');
            } else {
                $createdDate = Carbon::parse($createdDate)->format('Y-m-d H:i:s');
            }
        }

        // ----------------------------
        // 2. Clean phone number
        // ----------------------------
        $recipientPhone = $row['recipient_phone'] ?? null;
        if ($recipientPhone) {
            // Remove surrounding quotes
            $recipientPhone = trim($recipientPhone, "\"'");
            // Cast numeric to string
            if (is_numeric($recipientPhone)) {
                $recipientPhone = number_format($recipientPhone, 0, '', '');
            } else {
                $recipientPhone = (string) $recipientPhone;
            }
        }

        // ----------------------------
        // 3. Find order id via merchant_order_id
        // ----------------------------
        $orderId = null;
        if (!empty($row['merchant_order_id'])) {
            $order = Order::where('invoice_id', trim($row['merchant_order_id']))->first();
            $orderId = $order?->id;
        }

        // ----------------------------
        // 4. Create courier invoice
        // ----------------------------
        return new CourierPaidInvoice([
            'consignment_id'      => (string) ($row['consignment_id'] ?? null),
            'created_date'        => $createdDate,
            'invoice_type'        => $row['invoice_type'] ?? null,

            'collected_amount'    => $row['collected_amount'] ?? 0,
            'recipient_name'      => $row['recipient_name'] ?? null,
            'recipient_phone'     => $recipientPhone,

            'collectable_amount'  => $row['collectable_amount'] ?? 0,
            'cod_fee'             => $row['cod_fee'] ?? 0,
            'delivery_fee'        => $row['delivery_fee'] ?? 0,
            'final_fee'           => $row['final_fee'] ?? 0,

            'discount'            => $row['discount'] ?? 0,
            'additional_charge'   => $row['additional_charge'] ?? 0,
            'compensation_cost'   => $row['compensation_cost'] ?? 0,
            'promo_discount'      => $row['promo_discount'] ?? 0,
            'payout'              => $row['payout'] ?? 0,

            'merchant_order_id'   => $row['merchant_order_id'] ?? null,
            'order_id'            => $orderId,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.courier_name'      => 'required|string|max:100',
            '*.consignment_id'    => [
                'required',
                'string',
                Rule::unique('courier_paid_invoices', 'consignment_id'),
            ],
            '*.created_date'      => ['required', function ($attribute, $value, $fail) {
                if (is_numeric($value)) {
                    try {
                        ExcelDate::excelToDateTimeObject($value);
                        return;
                    } catch (\Throwable $e) {
                        return $fail("Invalid Excel date value.");
                    }
                }
                if (!strtotime($value)) {
                    return $fail("Invalid date format.");
                }
            }],
            '*.invoice_type'      => 'nullable|string|max:50',
            '*.collected_amount'  => 'nullable|numeric|min:0',
            '*.recipient_name'    => 'nullable|string|max:255',
            '*.recipient_phone'   => 'nullable',
            '*.collectable_amount' => 'nullable|numeric|min:0',
            '*.cod_fee'            => 'nullable|numeric|min:0',
            '*.delivery_fee'       => 'nullable|numeric|min:0',
            '*.final_fee'          => 'nullable|numeric|min:0',
            '*.discount'           => 'nullable|numeric|min:0',
            '*.additional_charge'  => 'nullable|numeric|min:0',
            '*.compensation_cost'  => 'nullable|numeric|min:0',
            '*.promo_discount'     => 'nullable|numeric|min:0',
            '*.payout'             => 'nullable|numeric|min:0',
            '*.merchant_order_id'  => 'nullable|string|max:255||unique:courier_paid_invoices,merchant_order_id',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.consignment_id.unique' => 'Consignment ID already exists.',
            '*.created_date.required' => 'Created date is required.',
            '*.created_date.*'        => 'Invalid created date.',
        ];
    }
}
