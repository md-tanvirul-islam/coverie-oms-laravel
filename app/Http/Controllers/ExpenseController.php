<?php

namespace App\Http\Controllers;

use App\DataTables\ExpensesDataTable;
use App\Exports\ExpensesExport;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Imports\ExpensesImport;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $service) {}

    public function index(ExpensesDataTable $dataTable)
    {
        return $dataTable->render('expenses.index');
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $data['created_by'] = Auth::id();

        $this->service->create($data);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense created.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($expense, $data);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $this->service->delete($expense);

        return back()->with('success', 'Expense deleted.');
    }

    public function import()
    {
        return view('expenses.import');
    }

    public function importStore(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);

        try {
            $import = new ExpensesImport;
            Excel::import($import, $request->file('file'));
            if ($import->failures()->isNotEmpty()) {
                $errors = [];
                foreach ($import->failures() as $failure) {
                    $errors[] = [
                        "row" => $failure->row(),
                        "errors" => implode(',', $failure->errors()),
                    ];
                }
                return back()->with("import_errors", $errors);
            }

            return back()->with('success', 'Excel imported successfully.');
        } catch (\Exception $e) {
            Log::error('Expense import failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Import failed. Please check the file and try again.');
        }
    }

    public function export()
    {
        $time = now()->format('Y-m-d_H-i-s');
        return Excel::download(new ExpensesExport, "expenses_{$time}.xlsx");
    }
}
