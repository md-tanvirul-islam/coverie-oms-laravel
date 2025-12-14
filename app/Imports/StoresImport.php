<?php

namespace App\Imports;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
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
        return new Store([
            'name' => $row['name'],
            'type' => $row['type'],
            'status' => $row['status'],
            'created_by' => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:stores,name',
            'type' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ];
    }
}
