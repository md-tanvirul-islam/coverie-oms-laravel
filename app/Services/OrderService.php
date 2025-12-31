<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function create(array $data)
    {
        $total_quantity = 0;
        $sub_total = 0.00;
        $discount = 0.00;
        $total_cost = 0.00;

        foreach ($data['items'] as $item) {
            $total_quantity += $item['quantity'];
            $sub_total += $item['unit_price'] * $item['quantity'];
        }

        $discount = $data['discount'] ?? 0.00;
        $total_cost = $sub_total - $discount;

        $order_data = [
            'store_id' => $data['store_id'],
            'team_id' => $data['team_id'],
            'invoice_code' => $data['invoice_code'],
            'taker_employee_id' => $data['taker_employee_id'],
            'order_date' => $data['order_date'],
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_address' => $data['customer_address'],
            'total_quantity' => $total_quantity,
            'sub_total' => $sub_total,
            'discount' => $discount,
            'total_cost' => $total_cost,
        ];

        $order = Order::create($order_data);

        foreach ($data['items'] as $item) {
            $item = $order->items()->create([
                'team_id' => $data['team_id'],
                'store_id' => $data['store_id'],
                'item_id' => $item['item_id'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'attributes' => $item['attributes'] ?? null,
            ]);

            if (isset($item['documents'])) {
                $item->documents()->store($item['documents']);
            }
        }

        return $order;
    }

    public function update(Order $order, array $data)
    {
        return $order->update($data);
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }
}
