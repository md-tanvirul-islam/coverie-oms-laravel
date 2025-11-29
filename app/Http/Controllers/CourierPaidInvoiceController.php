<?php

namespace App\Http\Controllers;

use App\DataTables\CourierPaidInvoicesDataTable;
use App\Exports\CourierPaidInvoiceExport;
use App\Http\Requests\CourierInvoice\StoreCourierPaidInvoiceRequest;
use App\Http\Requests\CourierInvoice\UpdateCourierPaidInvoiceRequest;
use App\Imports\CourierPaidInvoiceImport;
use App\Services\CourierPaidInvoiceService;
use App\Models\CourierPaidInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CourierPaidInvoiceController extends Controller
{
    public function __construct(private CourierPaidInvoiceService $service) {}

    public function index(CourierPaidInvoicesDataTable $dataTable)
    {
        return $dataTable->render('courier_paid_invoices.index');
    }

    public function create()
    {
        return view('courier_paid_invoices.create');
    }

    public function store(StoreCourierPaidInvoiceRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('courier_paid_invoices.index')->with('success', 'Courier Paid Invoice created.');
    }

    public function edit(CourierPaidInvoice $courier_paid_invoice)
    {
        return view('courier_paid_invoices.edit', compact('courier_paid_invoice'));
    }

    public function update(UpdateCourierPaidInvoiceRequest $request, CourierPaidInvoice $courier_paid_invoice)
    {
        $this->service->update($courier_paid_invoice, $request->validated());
        return redirect()->route('courier_paid_invoices.index')->with('success', 'Courier Paid Invoice updated.');
    }

    public function destroy(CourierPaidInvoice $courier_paid_invoice)
    {
        $this->service->delete($courier_paid_invoice);
        return back()->with('success', 'Courier Paid Invoice deleted.');
    }

    public function import()
    {
        return view('courier_paid_invoices.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
            'courier_name' => ['required', Rule::in(array_values(config('constants.couriers')))],

        ]);

        try {
            $import = new CourierPaidInvoiceImport($request->input('courier_name'));
            Excel::import($import, $request->file('file'));

            dd($import->failures());

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
            Log::error('Courier Paid Invoice import failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Import failed. Please check the file and try again.');
        }
    }

    // Excel Export
    public function export()
    {
        return Excel::download(new CourierPaidInvoiceExport, 'Courier_Paid_Invoices.xlsx');
    }
}
