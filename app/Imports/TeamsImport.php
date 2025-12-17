<?php

namespace App\Imports;

use App\Models\Team;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class TeamsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new Team([
            'name' => $row['name'],
            'status' => $row['status'],
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by']
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:stores,name',
            'status' => 'required|boolean',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer'
        ];
    }
}
