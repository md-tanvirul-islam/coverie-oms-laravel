<?php

namespace App\Services;

use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrdersImport;
use App\Exports\OrdersExport;

class OrderService
{
    public function create(array $data)
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data)
    {
        return $order->update($data);
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }

    public function import($request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        Excel::import(new OrdersImport, $request->file('file'));
    }

    public function export()
    {
        return Excel::download(new OrdersExport, 'Orders.xlsx');
    }
}
