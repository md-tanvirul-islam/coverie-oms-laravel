<?php

namespace App\Http\Controllers;

use App\DataTables\StoresDataTable;
use App\Exports\StoresExport;
use App\Http\Requests\Stores\StoreStoresRequest;
use App\Http\Requests\Stores\UpdateStoresRequest;
use App\Imports\StoresImport;
use App\Models\Stores;
use App\Services\StoresService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class StoresController extends Controller
{
    public function __construct(private StoresService $service) {}

    public function index(StoresDataTable $dataTable)
    {
        return $dataTable->render('stores.index');
    }

    public function create()
    {
        return view('stores.create');
    }

    public function store(StoreStoresRequest $request)
    {
        $data = $request->validated();

        $data['created_by'] = Auth::id();

        $this->service->create($data);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Stores created.');
    }

    public function edit(Stores $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(UpdateStoresRequest $request, Stores $store)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($store, $data);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Stores updated.');
    }

    public function destroy(Stores $store)
    {
        $this->service->delete($store);

        return back()->with('success', 'Stores deleted.');
    }

    public function import()
    {
        return view('stores.import');
    }

    public function importStore(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);

        try {
            $import = new StoresImport;
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
            Log::error('Stores import failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Import failed. Please check the file and try again.');
        }
    }

    public function export()
    {
        return Excel::download(new StoresExport, 'Stores.xlsx');
    }
}
