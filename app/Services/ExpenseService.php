<?php

namespace App\Services;

use App\Models\Expense;

class ExpenseService
{
    public function list($data = [], $is_query_only = false, $is_paginated = true)
    {
        $order = "asc";

        $query = Expense::query();

        $query = $query->with([]);

        //filter options
                if(isset($data['expense_type_id'])) {
            $query->where('expense_type_id', $data['expense_type_id']);
        }

        if(isset($data['store_id'])) {
            $query->where('store_id', $data['store_id']);
        }

        if(isset($data['employee_id'])) {
            $query->where('employee_id', $data['employee_id']);
        }

        if(isset($data['amount'])) {
            $query->where('amount', $data['amount']);
        }

        if(isset($data['expense_date'])) {
            $query->where('expense_date', $data['expense_date']);
        }

        if(isset($data['reference'])) {
            $query->where('reference', $data['reference']);
        }

        if(isset($data['created_by'])) {
            $query->where('created_by', $data['created_by']);
        }

        if(isset($data['updated_by'])) {
            $query->where('updated_by', $data['updated_by']);
        }

        if ($is_query_only === true) {
            return $query;
        }

        if ($is_paginated === true) {
            $item_per_page = isset($data['item_per_page']) ? $data['item_per_page'] : config('constants.pagination.per_page');
            $expenses = $query->paginate($item_per_page)->appends($data);
            $expenses->pagination_summary = get_pagination_summary($expenses);
        } else {
            $expenses = $query->get();
        }

        return $expenses;
    }

    public function create(array $data)
    {
        return Expense::create($data);
    }

    public function update(Expense $model, array $data)
    {
        return $model->update($data);
    }

    public function delete(Expense $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return Expense::findOrFail($id);
    }
}
