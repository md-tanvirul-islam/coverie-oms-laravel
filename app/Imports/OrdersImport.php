<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\ToModel;

class OrdersImport implements ToModel
{
    public function model(array $row)
    {
        return new Order([
            'invoice_id'       => $row[0],
            'order_date'       => $row[1],
            'customer_name'    => $row[2],
            'customer_phone'   => $row[3],
            'customer_address' => $row[4],
            'total_cost'       => $row[5],
            'phone_model'      => $row[6],
            'moderator_id'     => $row[7],
        ]);
    }
}
