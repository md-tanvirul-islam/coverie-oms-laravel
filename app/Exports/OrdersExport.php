<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class OrdersExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStrictNullComparison
{
    public function collection()
    {
        return Order::query()
            ->with(['employee:id,name,code', 'store:id,name'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Store',
            'Invoice ID',
            'Order Date',
            'Customer Name',
            'Customer Phone',
            'Customer Address',
            'Total Cost',
            'Phone Model',
            'Order Taken By',
            'Order Taken By(Name)',
            'Created At',
        ];
    }

    public function map($order): array
    {
        return [
            optional($order->store)->name,
            $order->invoice_id,
            $order->order_date,
            $order->customer_name,
            $order->customer_phone,
            $order->customer_address,
            $order->total_cost,
            $order->phone_model,
            optional($order->employee)->code,
            optional($order->employee)->name_and_code,
            $order->created_at,
        ];
    }
}
