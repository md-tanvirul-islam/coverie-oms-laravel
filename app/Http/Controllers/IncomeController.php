<?php

namespace App\Http\Controllers;

use App\DataTables\IncomesDataTable;
use App\Exports\IncomesExport;
use App\Http\Requests\Income\StoreIncomeRequest;
use App\Http\Requests\Income\UpdateIncomeRequest;
use App\Imports\IncomesImport;
use App\Models\Income;
use App\Services\IncomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class IncomeController extends Controller
{
    public function __construct(private IncomeService $service) {}

    public function index(IncomesDataTable $dataTable)
    {
        return $dataTable->render('incomes.index');
    }

    public function create()
    {
        return view('incomes.create');
    }

    public function store(StoreIncomeRequest $request)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $data['created_by'] = Auth::id();

        $this->service->create($data);

        return redirect()
            ->route('incomes.index')
            ->with('success', 'Income created.');
    }

    public function edit(Income $income)
    {
        return view('incomes.edit', compact('income'));
    }

    public function update(UpdateIncomeRequest $request, Income $income)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($income, $data);

        return redirect()
            ->route('incomes.index')
            ->with('success', 'Income updated.');
    }

    public function destroy(Income $income)
    {
        $this->service->delete($income);

        return back()->with('success', 'Income deleted.');
    }

    public function import()
    {
        return view('incomes.import');
    }

    public function importStore(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);

        try {
            $import = new IncomesImport;
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
            Log::error('Income import failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Import failed. Please check the file and try again.');
        }
    }

    public function export()
    {
        $time = now()->format('Y-m-d_H-i-s');
        return Excel::download(new IncomesExport, "incomes_{$time}.xlsx");
    }
}
