<?php

namespace App\Http\Controllers;

use App\DataTables\OrdersDataTable;
use App\Exports\OrdersExport;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Imports\OrderImport;
use App\Models\Item;
use App\Services\OrderService;
use App\Models\Order;
use App\Services\EmployeeService;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function __construct(private OrderService $service) {}

    public function index(OrdersDataTable $dataTable)
    {
        return $dataTable->render('orders.index');
    }

    public function create(EmployeeService $employeeService, ItemService $itemService)
    {
        $employees = $employeeService->dropdown();
        $items = $itemService->dropdown();
        return view('orders.create', compact('employees', 'items'));
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $data['team_id'] = getPermissionsTeamId();

        try {
            DB::beginTransaction();
            $this->service->create($data);
            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order created.');
        } catch (\Exception $e) {
            DB::rollBack();
            log_exception($e, 'Order creation failed');
            return back()->with('error', 'Order creation failed. Please try again.');
        }
    }

    public function edit(Order $order, EmployeeService $employeeService, ItemService $itemService)
    {
        $employees = $employeeService->dropdown();
        $items = $itemService->dropdown();
        return view('orders.edit', compact('order', 'employees', 'items'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();
        $data['team_id'] = getPermissionsTeamId();

        try {
            DB::beginTransaction();
            $this->service->update($order, $data);
            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            log_exception($e, 'Order update failed');
            return back()->with('error', 'Order update failed. Please try again.');
        }
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
