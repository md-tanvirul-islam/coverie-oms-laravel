<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Item;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
    }

    private function addOrderItemsInTheOrder(&$orders, &$order_items)
    {
        $orders[count($orders) - 1]['order_items'] = $order_items;
        $order_items = [];
        
        // Persist orders batch when adding items to last order
        $this->persistOrders($orders);
    }

    /**
     * Persist orders and their items to the database
     * Uses transaction to ensure data consistency
     */
    private function persistOrders(array &$orders): void
    {
        if (empty($orders)) {
            return;
        }

        DB::transaction(function () use (&$orders) {
            foreach ($orders as &$order_data) {
                $order_items = $order_data['order_items'] ?? [];
                unset($order_data['order_items']);
                
                // Map import data to Order model fields
                $mapped_order = $this->mapOrderData($order_data);
                
                // Create order record
                $order = Order::create($mapped_order);
                
                // Create associated order items
                foreach ($order_items as $item_data) {
                    if (!empty($item_data['item'])) {
                        $this->createOrderItem($order, $item_data);
                    }
                }
            }
        });

        // Clear orders array after persistence
        $orders = [];
    }

    /**
     * Map import row data to Order model fillable fields
     */
    private function mapOrderData(array $row): array
    {
        // Get store by name
        $store = Store::where('name', trim($row['store'] ?? ''))->first();
        
        // Get employee (taker) by code
        $employee = Employee::where('code', trim($row['order_taken_by'] ?? ''))->first();
        
        // Parse order date
        $order_date = $this->parseDate($row['order_date'] ?? now());
        
        return [
            'team_id'          => getPermissionsTeamId(),
            'store_id'         => $store?->id,
            'invoice_code'     => (string) ($row['invoice_id'] ?? ''),
            'order_date'       => $order_date,
            'customer_name'    => $row['customer_name'] ?? '',
            'customer_phone'   => (string) ($row['customer_phone'] ?? ''),
            'customer_address' => $row['customer_address'] ?? null,
            'taker_employee_id' => $employee?->id,
            'discount'         => (float) ($row['discount'] ?? 0),
        ];
    }

    /**
     * Create order item with attributes and documents
     */
    private function createOrderItem(Order $order, array $item_data): void
    {
        $item = Item::where('name', trim($item_data['item'] ?? ''))->first();
        
        if (!$item) {
            return;
        }

        $order_item = $order->items()->create([
            'item_id'      => $item->id,
            'unit_price'   => (float) ($item_data['unit_price'] ?? 0),
            'quantity'     => (int) ($item_data['quantity'] ?? 1),
        ]);

        // Store attributes if provided
        if (!empty($item_data['attributes'])) {
            // Store as JSON or separate records depending on your schema
            $order_item->update(['attributes' => $item_data['attributes']]);
        }
    }

    /**
     * Parse date from various formats (string or Excel numeric)
     */
    private function parseDate($value): string
    {
        if (is_numeric($value)) {
            try {
                // Convert Excel numeric date
                $date = Carbon::instance(ExcelDate::excelToDateTimeObject($value));
            } catch (\Exception $e) {
                $date = Carbon::now();
            }
        } else {
            // Parse string date
            $date = Carbon::parse($value);
        }
        
        return $date->format('Y-m-d');
    }

    public function rules(): array
    {
        return [
            '*.store'              => 'required|string',
            '*.invoice_id'         => 'required|string',
            '*.order_date'         => 'required',
            '*.customer_name'      => 'required|string',
            '*.customer_phone'     => 'required',
            '*.customer_address'   => 'nullable|string',
            '*.order_taken_by'     => 'required|string',
            '*.item'               => 'required|string',
            '*.unit_price'         => 'required|numeric|min:0',
            '*.quantity'           => 'required|integer|min:1',
            '*.discount'           => 'nullable|numeric|min:0',
            '*.attributes'         => 'nullable|string',
            '*.documents'          => 'nullable|string',
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
