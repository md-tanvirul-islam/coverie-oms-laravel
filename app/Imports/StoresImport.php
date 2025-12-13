<?php

namespace App\Imports;

use App\Models\Stores;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class StoresImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new Stores([
            'user_id' => $row['user_id'],
            'name' => $row['name'],
            'slug' => $row['slug'],
            'type' => $row['type'],
            'logo' => $row['logo'],
            'status' => $row['status'],
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by']
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255|unique:stores,name',
            'slug' => 'required|string|max:255|unique:stores,slug',
            'type' => 'nullable|string|max:100',
            'logo' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer'
        ];
    }
}
