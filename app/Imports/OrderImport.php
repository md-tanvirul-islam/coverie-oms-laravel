<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

// class OrderImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
class OrderImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function collection(Collection $collection)
    {
        $orders = [];
        $order_items = [];
        $row_count = $collection->count();

        // dd($row_count);

        // Process the imported collection data here
        foreach ($collection as $row_index =>  $row) {
            if (isset($row['invoice_id'])) {
                if (count($orders)) {
                    $this->addOrderItemsInTheOrder($orders, $order_items);
                }

                $order = [
                    'store' => $row['store'],
                    'invoice_id' => $row['invoice_id'],
                    'order_date' => $row['order_date'],
                    'customer_name' => $row['customer_name'],
                    'customer_phone' => $row['customer_phone'],
                    'customer_address' => $row['customer_address'],
                    'order_taken_by' => $row['order_taken_by'],
                    'courier_fee_type' => $row['courier_fee_type'],
                    'discount' => $row['discount'],
                ];

                $orders[] = $order;
            }

            $order_items[] = [
                'item' => $row['item'],
                'attributes' => $row['attributes'],
                'documents' => $row['documents'],
                'unit_price' => $row['unit_price'],
                'quantity' => $row['quantity'],
            ];

            if ($row_index === $row_count - 1) {
                $this->addOrderItemsInTheOrder($orders, $order_items);
            }
        }

        // dd($orders);
    }

    private function addOrderItemsInTheOrder(&$orders, &$order_items)
    {
        $orders[count($orders) - 1]['order_items'] = $order_items;
        $order_items = [];
    }

    // public function model(array $row)
    // {
    //     dd($row);

    //     /**
    //      * Convert Excel numeric date to Carbon date.
    //      * Excel stores dates as numbers (e.g., 45321).
    //      */
    //     $orderDate = $row['order_date'];

    //     if (is_numeric($orderDate)) {
    //         // Convert excel date number → PHP date string
    //         $orderDate = Carbon::instance(ExcelDate::excelToDateTimeObject($orderDate))
    //             ->format('Y-m-d');
    //     } else {
    //         // Treat as normal string date
    //         $orderDate = Carbon::parse($orderDate)->format('Y-m-d');
    //     }


    //     /**
    //      * Convert phone number to string (prevent scientific notation issues)
    //      */
    //     $customerPhone = $row['customer_phone'];

    //     if (is_numeric($customerPhone)) {
    //         $customerPhone = number_format($customerPhone, 0, '', '');
    //     } else {
    //         $customerPhone = (string) $customerPhone;
    //     }

    //     $store = Store::where('name', $row['store'])->first(['id']);
    //     $employee = Employee::where('code', $row['order_taken_by'])->first(['id']);

    //     return new Order([
    //         'employee_id'     => $store?->id,
    //         'invoice_code'       => (string) $row['invoice_code'],
    //         'order_date'       => $orderDate,
    //         'customer_name'    => $row['customer_name'],
    //         'customer_phone'   => $customerPhone,
    //         'customer_address' => $row['customer_address'] ?? null,
    //         'quantity'         => $row['quantity'],
    //         'total_cost'       => $row['total_cost'],
    //         'phone_model'      => $row['phone_model'],
    //         'employee_id'     => $employee?->id,
    //     ]);
    // }

    public function rules(): array
    {
        return [
            // '*.store'            => 'required|exists:stores,name',
            // '*.invoice_code'       => ['required', 'string', Rule::unique('orders', 'invoice_code')],
            // '*.order_date'       => ['required', function ($attribute, $value, $fail) {

            //     // Accept numeric Excel dates
            //     if (is_numeric($value)) {
            //         try {
            //             \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            //             return;
            //         } catch (\Exception $e) {
            //             return $fail("Invalid Excel date value.");
            //         }
            //     }

            //     // Accept normal date strings
            //     if (!strtotime($value)) {
            //         return $fail("Invalid date format.");
            //     }
            // }],
            // '*.customer_name'    => 'required|string',
            // '*.customer_phone'   => 'required',
            // '*.customer_address' => 'nullable|string',
            // '*.total_cost'       => 'required|numeric|min:0',
            // '*.phone_model'      => 'required|string|max:255',
            // '*.order_taken_by'   => 'required|exists:employees,code',
            // '*.quantity'         => 'required|integer|min:1',
        ];
    }


    public function customValidationMessages()
    {
        return [
            '*.invoice_code.unique' => 'Invoice ID already exists.',
            '*.order_taken_by.exists' => 'Employee code not found.',
            '*.order_date.required' => 'Order date is required.',
        ];
    }
}
