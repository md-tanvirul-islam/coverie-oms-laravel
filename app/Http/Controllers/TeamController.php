<?php

namespace App\Http\Controllers;

use App\DataTables\TeamsDataTable;
use App\Exports\TeamsExport;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Imports\TeamsImport;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TeamController extends Controller
{
    public function __construct(private TeamService $service) {}

    public function index(TeamsDataTable $dataTable)
    {
        return $dataTable->render('teams.index');
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(StoreTeamRequest $request)
    {
        $data = $request->validated();

        $data['created_by'] = Auth::id();

        $this->service->create($data);

        return redirect()
            ->route('teams.index')
            ->with('success', 'Team created.');
    }

    public function edit(Team $team)
    {
        return view('teams.edit', compact('team'));
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $this->service->update($team, $data);

        return redirect()
            ->route('teams.index')
            ->with('success', 'Team updated.');
    }

    public function destroy(Team $team)
    {
        $this->service->delete($team);

        return back()->with('success', 'Team deleted.');
    }

    public function import()
    {
        return view('teams.import');
    }

    public function importStore(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);

        try {
            $import = new TeamsImport;
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
            Log::error('Team import failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Import failed. Please check the file and try again.');
        }
    }

    public function export()
    {
        return Excel::download(new TeamsExport, 'Teams.xlsx');
    }
}
