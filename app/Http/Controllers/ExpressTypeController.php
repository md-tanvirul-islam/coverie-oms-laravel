<?php

namespace App\Http\Controllers;

use App\DataTables\ExpressTypesDataTable;
use App\Exports\ExpressTypesExport;
use App\Http\Requests\ExpressType\StoreExpressTypeRequest;
use App\Http\Requests\ExpressType\UpdateExpressTypeRequest;
use App\Imports\ExpressTypesImport;
use App\Models\ExpressType;
use App\Services\ExpressTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExpressTypeController extends Controller
{
    public function __construct(private ExpressTypeService $service) {}

    public function index(ExpressTypesDataTable $dataTable)
    {
        return $dataTable->render('express_types.index');
    }

    public function create()
    {
        return view('express_types.create');
    }

    public function store(StoreExpressTypeRequest $request)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $data['created_by'] = Auth::id();

        $this->service->create($data);

        return redirect()
            ->route('express_types.index')
            ->with('success', 'Express Type created.');
    }

    public function edit(ExpressType $express_type)
    {
        return view('express_types.edit', compact('express_type'));
    }

    public function update(UpdateExpressTypeRequest $request, ExpressType $express_type)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($express_type, $data);

        return redirect()
            ->route('express_types.index')
            ->with('success', 'Express Type updated.');
    }

    public function destroy(ExpressType $express_type)
    {
        $this->service->delete($express_type);

        return back()->with('success', 'Express Type deleted.');
    }
}
