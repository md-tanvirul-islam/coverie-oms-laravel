<?php

namespace App\Services;

use App\Models\ExpenseType;

class ExpenseTypeService
{
    public function list($data = [], $is_query_only = false, $is_paginated = true)
    {
        $order = "asc";

        $query = ExpenseType::query();

        $query = $query->with([]);

        //filter options
        if (isset($data['name'])) {
            $query->where('name', $data['name']);
        }

        if (array_key_exists('is_active', $data)) {
            $query->where('is_active', $data['is_active']);
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
            $expense_types = $query->paginate($item_per_page)->appends($data);
            $expense_types->pagination_summary = get_pagination_summary($expense_types);
        } else {
            $expense_types = $query->get();
        }

        return $expense_types;
    }

    public function create(array $data)
    {
        return ExpenseType::create($data);
    }

    public function update(ExpenseType $model, array $data)
    {
        return $model->update($data);
    }

    public function delete(ExpenseType $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return ExpenseType::findOrFail($id);
    }

    public function dropdown()
    {
        return ExpenseType::pluck('name', 'id')->toArray();
    }
}
