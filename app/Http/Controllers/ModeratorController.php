<?php

namespace App\Http\Controllers;

use App\DataTables\ModeratorsDataTable;
use App\Http\Requests\Moderator\StoreModeratorRequest;
use App\Http\Requests\Moderator\UpdateModeratorRequest;
use App\Models\Moderator;
use App\Services\ModeratorService;

class ModeratorController extends Controller
{
    public function __construct(private ModeratorService $service) {}

    public function index(ModeratorsDataTable $dataTable)
    {
        return $dataTable->render('moderators.index');
    }

    public function create()
    {
        return view('moderators.create');
    }

    public function store(StoreModeratorRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('moderators.index')
            ->with('success', 'Moderator created successfully.');
    }

    public function edit(Moderator $moderator)
    {
        return view('moderators.edit', compact('moderator'));
    }

    public function update(UpdateModeratorRequest $request, Moderator $moderator)
    {
        $this->service->update($moderator, $request->validated());

        return redirect()
            ->route('moderators.index')
            ->with('success', 'Moderator updated.');
    }

    public function destroy(Moderator $moderator)
    {
        $this->service->delete($moderator);

        return back()->with('success', 'Moderator deleted.');
    }
}
