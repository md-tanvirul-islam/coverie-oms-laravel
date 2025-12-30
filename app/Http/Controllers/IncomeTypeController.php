<?php

namespace App\Http\Controllers;

use App\DataTables\IncomeTypesDataTable;
use App\Exports\IncomeTypesExport;
use App\Http\Requests\IncomeType\StoreIncomeTypeRequest;
use App\Http\Requests\IncomeType\UpdateIncomeTypeRequest;
use App\Imports\IncomeTypesImport;
use App\Models\IncomeType;
use App\Services\IncomeTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class IncomeTypeController extends Controller
{
    public function __construct(private IncomeTypeService $service) {}

    public function index(IncomeTypesDataTable $dataTable)
    {
        return $dataTable->render('income_types.index');
    }

    public function create()
    {
        return view('income_types.create');
    }

    public function store(StoreIncomeTypeRequest $request)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $data['created_by'] = Auth::id();

        $this->service->create($data);

        return redirect()
            ->route('income_types.index')
            ->with('success', 'Income Type created.');
    }

    public function edit(IncomeType $income_type)
    {
        return view('income_types.edit', compact('income_type'));
    }

    public function update(UpdateIncomeTypeRequest $request, IncomeType $income_type)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($income_type, $data);

        return redirect()
            ->route('income_types.index')
            ->with('success', 'Income Type updated.');
    }

    public function destroy(IncomeType $income_type)
    {
        $this->service->delete($income_type);

        return back()->with('success', 'Income Type deleted.');
    }
}
