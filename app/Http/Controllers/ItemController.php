<?php

namespace App\Http\Controllers;

use App\DataTables\ItemsDataTable;
use App\Exports\ItemsExport;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Imports\ItemsImport;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $this->service->create($data);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item created.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($item, $data);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        $this->service->delete($item);

        return back()->with('success', 'Item deleted.');
    }
}
