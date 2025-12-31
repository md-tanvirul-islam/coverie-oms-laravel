<?php

namespace App\Services;

use App\Models\Income;
use Illuminate\Support\Facades\Storage;

class IncomeService
{
    public function list($data = [], $is_query_only = false, $is_paginated = true)
    {
        $order = "asc";

        $query = Income::query();

        $query = $query->with([]);

        //filter options
        if (isset($data['income_type_id'])) {
            $query->where('income_type_id', $data['income_type_id']);
        }

        if (isset($data['store_id'])) {
            $query->where('store_id', $data['store_id']);
        }

        if (isset($data['employee_id'])) {
            $query->where('employee_id', $data['employee_id']);
        }

        if (isset($data['amount'])) {
            $query->where('amount', $data['amount']);
        }

        if (isset($data['income_date'])) {
            $query->where('income_date', $data['income_date']);
        }

        if (isset($data['reference'])) {
            $query->where('reference', $data['reference']);
        }

        if (isset($data['created_by'])) {
            $query->where('created_by', $data['created_by']);
        }

        if (isset($data['updated_by'])) {
            $query->where('updated_by', $data['updated_by']);
        }

        if ($is_query_only === true) {
            return $query;
        }

        if ($is_paginated === true) {
            $item_per_page = isset($data['item_per_page']) ? $data['item_per_page'] : config('constants.pagination.per_page');
            $incomes = $query->paginate($item_per_page)->appends($data);
            $incomes->pagination_summary = get_pagination_summary($incomes);
        } else {
            $incomes = $query->get();
        }

        return $incomes;
    }

    public function create(array $data)
    {
        $income = Income::create($data);

        if (!empty($data['documents']) && $income) {

            $income->documents()->store($data['documents']);
        }
    }

    public function update(Income $income, array $data)
    {
        $income->update($data);

        if (!empty($data['delete_documents'])) {
            $existingArtifacts = $income->documents()->whereIn('id', $data['delete_documents'] ?? [])->get();

            foreach ($existingArtifacts as $artifact) {
                // Delete the file from storage
                Storage::disk($artifact->disk)->delete($artifact->path);
                // Delete the database record
                $artifact->delete();
            }
        }

        if (!empty($data['documents'])) {
            $income->documents()->store($data['documents']);
        }

        return $income;
    }

    public function delete(Income $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return Income::findOrFail($id);
    }
}
