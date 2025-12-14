<?php

namespace App\Http\Controllers;

use App\DataTables\StoresDataTable;
use App\Exports\StoresExport;
use App\Http\Requests\Store\FilterStoreRequest;
use App\Http\Requests\Store\StoreStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Imports\StoresImport;
use App\Models\Store;
use App\Services\StoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class StoreController extends Controller
{
    public function __construct(private StoreService $service) {}

    public function index(StoresDataTable $dataTable)
    {
        return $dataTable->render('stores.index');
    }

    public function create()
    {
        return view('stores.create');
    }

    public function store(StoreStoreRequest $request)
    {
        $data = $request->validated();

        $data['created_by'] = Auth::id();

        $this->service->create($data);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Store created.');
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(UpdateStoreRequest $request, Store $store)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($store, $data);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Store updated.');
    }

    public function destroy(Store $store)
    {
        $this->service->delete($store);

        return back()->with('success', 'Store deleted.');
    }

    public function show(Store $store)
    {
        return view('stores.show', compact('store'));
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
            Log::error('Store import failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Import failed. Please check the file and try again.');
        }
    }

    public function export(FilterStoreRequest $request)
    {
        return Excel::download(new StoresExport($request->validated()), 'Stores.xlsx');
    }
}
