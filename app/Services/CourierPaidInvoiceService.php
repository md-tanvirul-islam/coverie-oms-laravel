<?php

namespace App\Services;

use App\Models\CourierPaidInvoice;
use App\Models\Order;

class CourierPaidInvoiceService
{
    public function create(array $data): CourierPaidInvoice
    {
        $orderId = null;
        if (!empty($data['merchant_order_id'])) {
            $order = Order::where('invoice_code', trim($data['merchant_order_id']))->first();
            $orderId = $order?->id;
        }
        $data['order_id'] = $orderId;
        return CourierPaidInvoice::create($data);
    }

    public function update(CourierPaidInvoice $invoice, array $data): CourierPaidInvoice
    {
        $orderId = null;
        if (!empty($data['merchant_order_id'])) {
            $order = Order::where('invoice_code', trim($data['merchant_order_id']))->first();
            $orderId = $order?->id;
        }

        $data['order_id'] = $orderId;
        $invoice->update($data);
        return $invoice;
    }

    public function delete(CourierPaidInvoice $invoice): bool
    {
        return $invoice->delete();
    }
}
