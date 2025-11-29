<?php

namespace App\Exports;

use App\Models\CourierPaidInvoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CourierPaidInvoiceExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStrictNullComparison
{
    public function collection()
    {
        return CourierPaidInvoice::query()
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Courier Name',
            'Consignment ID',
            'Created Date',

            'Invoice Type',
            'Collected Amount',
            'Recipient Name',
            'Recipient Phone',

            'Collectable Amount',
            'COD Fee',
            'Delivery Fee',
            'Final Fee',

            'Discount',
            'Additional Charge',
            'Compensation Cost',
            'Promo Discount',
            'Payout',

            'Merchant Order ID',
            'Store Name',

            'Created At',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->courier_name,
            $invoice->consignment_id,
            $invoice->created_date,

            $invoice->invoice_type,
            $invoice->collected_amount,
            $invoice->recipient_name,
            $invoice->recipient_phone,

            $invoice->collectable_amount,
            $invoice->cod_fee,
            $invoice->delivery_fee,
            $invoice->final_fee,

            $invoice->discount,
            $invoice->additional_charge,
            $invoice->compensation_cost,
            $invoice->promo_discount,
            $invoice->payout,

            $invoice->merchant_order_id,

            $invoice->created_at,
        ];
    }
}
