<?php

namespace App\Imports;

use App\Models\Moderator;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class OrderImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        /**
         * Convert Excel numeric date to Carbon date.
         * Excel stores dates as numbers (e.g., 45321).
         */
        $orderDate = $row['order_date'];

        if (is_numeric($orderDate)) {
            // Convert excel date number → PHP date string
            $orderDate = Carbon::instance(ExcelDate::excelToDateTimeObject($orderDate))
                ->format('Y-m-d');
        } else {
            // Treat as normal string date
            $orderDate = Carbon::parse($orderDate)->format('Y-m-d');
        }


        /**
         * Convert phone number to string (prevent scientific notation issues)
         */
        $customerPhone = $row['customer_phone'];

        if (is_numeric($customerPhone)) {
            $customerPhone = number_format($customerPhone, 0, '', '');
        } else {
            $customerPhone = (string) $customerPhone;
        }

        $moderator = Moderator::where('code', $row['order_taken_by'])->first();

        return new Order([
            'invoice_id'       => (string) $row['invoice_id'],
            'order_date'       => $orderDate,
            'customer_name'    => $row['customer_name'],
            'customer_phone'   => $customerPhone,
            'customer_address' => $row['customer_address'] ?? null,
            'quantity'         => $row['quantity'],
            'total_cost'       => $row['total_cost'],
            'phone_model'      => $row['phone_model'],
            'moderator_id'     => $moderator?->id,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.invoice_id'       => ['required', 'string', Rule::unique('orders', 'invoice_id')],
            '*.order_date'       => ['required', function ($attribute, $value, $fail) {

                // Accept numeric Excel dates
                if (is_numeric($value)) {
                    try {
                        \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                        return;
                    } catch (\Exception $e) {
                        return $fail("Invalid Excel date value.");
                    }
                }

                // Accept normal date strings
                if (!strtotime($value)) {
                    return $fail("Invalid date format.");
                }
            }],
            '*.customer_name'    => 'required|string',
            '*.customer_phone'   => 'required',
            '*.customer_address' => 'nullable|string',
            '*.total_cost'       => 'required|numeric|min:0',
            '*.phone_model'      => 'required|string|max:255',
            '*.order_taken_by'   => 'required|exists:moderators,code',
            '*.quantity'         => 'required|integer|min:1',
        ];
    }


    public function customValidationMessages()
    {
        return [
            '*.invoice_id.unique' => 'Invoice ID already exists.',
            '*.order_taken_by.exists' => 'Moderator code not found.',
            '*.order_date.required' => 'Order date is required.',
        ];
    }
}
