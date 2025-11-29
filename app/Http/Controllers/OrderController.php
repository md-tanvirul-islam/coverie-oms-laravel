<?php

namespace App\Http\Controllers;

use App\DataTables\OrdersDataTable;
use App\Exports\OrdersExport;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Imports\OrderImport;
use App\Services\OrderService;
use App\Models\Order;
use App\Services\ModeratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function __construct(private OrderService $service) {}

    public function index(OrdersDataTable $dataTable)
    {
        return $dataTable->render('orders.index');
    }

    public function create(ModeratorService $moderatorService)
    {
        $moderators = $moderatorService->dropdown();
        return view('orders.create', compact('moderators'));
    }

    public function store(StoreOrderRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('orders.index')->with('success', 'Order created.');
    }

    public function edit(Order $order, ModeratorService $moderatorService)
    {
        $moderators = $moderatorService->dropdown();
        return view('orders.edit', compact('order', 'moderators'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $this->service->update($order, $request->validated());
        return redirect()->route('orders.index')->with('success', 'Order updated.');
    }

    public function destroy(Order $order)
    {
        $this->service->delete($order);
        return back()->with('success', 'Order deleted.');
    }

    public function import()
    {
        return view('orders.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        try {
            $import = new OrderImport;
            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                $errors = [];

                foreach ($import->failures() as $failure) {
                    dd($failure);
                    $errors[] = [
                        "row"    => $failure->row(),
                        "errors" => implode(',', $failure->errors()), 
                    ];
                }

                return back()->with("import_errors", $errors);
            }

            return back()->with('success', 'Excel imported successfully.');
        } catch (\Exception $e) {
            Log::error('Order import failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Import failed. Please check the file and try again.');
        }
    }

    // Excel Export
    public function export()
    {
        return Excel::download(new OrdersExport, 'Orders.xlsx');
    }
}
