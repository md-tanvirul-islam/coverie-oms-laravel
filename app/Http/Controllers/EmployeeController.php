<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeesDataTable;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\UserService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeService $service, private UserService $userService) {}

    public function index(EmployeesDataTable $dataTable)
    {
        return $dataTable->render('employees.index');
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $data['team_id'] = getPermissionsTeamId();

            if (isset($data['email'], $data['password'])) {
                $user = $this->userService->createUserAndAssignRoleTeamStore($data);

                if (! $user) {
                    throw new Exception("Failed to create user");
                }

                $data['user_id'] = $user->id;
            }

            $this->service->create($data);

            DB::commit();

            return redirect()
                ->route('employees.index')
                ->with('success', 'Employee created successfully.');
        } catch (Exception | QueryException $ex) {
            dd($ex);
            log_exception($ex, 'Fail to create Employee.');
            return redirect()
                ->route('employees.index')
                ->with('error', 'Failed! Try again later.');
        }
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->service->update($employee, $request->validated());

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $this->service->delete($employee);

        return back()->with('success', 'Employee deleted.');
    }
}
