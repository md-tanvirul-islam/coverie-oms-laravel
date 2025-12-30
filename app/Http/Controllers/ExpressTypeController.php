<?php

namespace App\Http\Controllers;

use App\DataTables\ExpenseTypesDataTable;
use App\Exports\ExpenseTypesExport;
use App\Http\Requests\ExpenseType\StoreExpenseTypeRequest;
use App\Http\Requests\ExpenseType\UpdateExpenseTypeRequest;
use App\Imports\ExpenseTypesImport;
use App\Models\ExpenseType;
use App\Services\ExpenseTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseTypeController extends Controller
{
    public function __construct(private ExpenseTypeService $service) {}

    public function index(ExpenseTypesDataTable $dataTable)
    {
        return $dataTable->render('expense_types.index');
    }

    public function create()
    {
        return view('expense_types.create');
    }

    public function store(StoreExpenseTypeRequest $request)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $data['created_by'] = Auth::id();

        $this->service->create($data);

        return redirect()
            ->route('expense_types.index')
            ->with('success', 'Expense Type created.');
    }

    public function edit(ExpenseType $expense_type)
    {
        return view('expense_types.edit', compact('expense_type'));
    }

    public function update(UpdateExpenseTypeRequest $request, ExpenseType $expense_type)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($expense_type, $data);

        return redirect()
            ->route('expense_types.index')
            ->with('success', 'Expense Type updated.');
    }

    public function destroy(ExpenseType $expense_type)
    {
        $this->service->delete($expense_type);

        return back()->with('success', 'Expense Type deleted.');
    }
}
