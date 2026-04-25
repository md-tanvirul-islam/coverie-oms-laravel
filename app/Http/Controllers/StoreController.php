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
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $data['team_id'] = getPermissionsTeamId();

        $data['created_by'] = Auth::id();

        try {
            DB::beginTransaction();

            $store = $this->service->create($data);

            $user_pivot_data = collect($data['user_ids'])->mapWithKeys(function ($user_id) use ($data) {
                return [
                    $user_id => [
                        'team_id'   => $data['team_id'],
                        'full_data' => $data['full_data_ar'][$user_id] ?? 0,
                    ],
                ];
            })->toArray();

            $store->users()->attach($user_pivot_data);

            DB::commit();

            return redirect()
                ->route('stores.index')
                ->with('success', 'Store created.');
        } catch (Exception | QueryException $ex) {
            DB::rollBack();
            log_exception($ex, 'Failed to create store');

            return redirect()
                ->route('stores.index')
                ->with('error', 'Failed! Try again later.');
        }
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(UpdateStoreRequest $request, Store $store)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $data['updated_by'] = Auth::id();

        try {
            DB::beginTransaction();

            $this->service->update($store, $data);

            $userPivotData = collect($data['user_ids'])->mapWithKeys(function ($user_id) use ($data) {
                return [
                    $user_id => [
                        'team_id'   => $data['team_id'],
                        'full_data' => $data['full_data_ar'][$user_id] ?? 0,
                    ],
                ];
            })->toArray();

            $store->users()->sync($userPivotData);

            DB::commit();

            return redirect()
                ->route('stores.index')
                ->with('success', 'Store updated.');
        } catch (Exception | QueryException $ex) {
            DB::rollBack();
            log_exception($ex, 'Failed to update store');
            return redirect()
                ->route('stores.index')
                ->with('error', 'Failed! Try again later.');
        }
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
