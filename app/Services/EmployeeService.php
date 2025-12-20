<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService
{
    public function create(array $data)
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data)
    {
        return $employee->update($data);
    }

    public function delete(Employee $employee)
    {
        return $employee->delete();
    }

    public function dropdown()
    {
        return Employee::select('id', 'name', 'code')
            ->get()
            ->mapWithKeys(function ($mod) {
                return [
                    $mod->id => "{$mod->name} ({$mod->code})"
                ];
            })
            ->toArray();
    }
}
