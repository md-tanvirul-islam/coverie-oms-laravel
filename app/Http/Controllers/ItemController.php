<?php

namespace App\Http\Controllers;

use App\DataTables\ItemsDataTable;
use App\Exports\ItemsExport;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Imports\ItemsImport;
use App\Models\Item;
use App\Services\ItemService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    public function __construct(private ItemService $service) {}

    public function index(ItemsDataTable $dataTable)
    {
        return $dataTable->render('items.index');
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(StoreItemRequest $request)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $data['created_by'] = Auth::id();

        $store_ids = $data['store_ids'];
        unset($data['store_ids']);

        try {
            DB::beginTransaction();

            foreach ($store_ids as $store_id) {
                $data['store_id'] = $store_id;
                $this->service->create($data);
            }
            
            DB::commit();

            return redirect()
                ->route('items.index')
                ->with('success', 'Item created.');
        } catch (Exception $e) {
            DB::rollBack();
            log_exception($e, 'Failed to create item.');
            return back()->withInput()->with('error', 'Failed to create item. Try again.');
        }
    }

    public function edit(Item $item)
    {
        $item = $item->load('attributes');
        return view('items.edit', compact('item'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $data['updated_by'] = Auth::id();

        try {
            DB::beginTransaction();

            $this->service->update($item, $data);

            DB::commit();

            return redirect()
                ->route('items.index')
                ->with('success', 'Item updated.');
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            log_exception($e, 'Failed to update item.');
            return back()->withInput()->with('error', 'Failed to update item. Try again.');
        }
    }

    public function destroy(Item $item)
    {
        $this->service->delete($item);

        return back()->with('success', 'Item deleted.');
    }
}
