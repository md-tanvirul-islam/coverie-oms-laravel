<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeesDataTable;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeService $service) {}

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
        // dd($request->validated(), $request->all());
        $this->service->create($request->validated());

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        // dd($request->validated(), $request->all());

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
