<?php

namespace App\Services;

use App\Models\IncomeType;

class IncomeTypeService
{
    public function list($data = [], $is_query_only = false, $is_paginated = true)
    {
        $order = "asc";

        $query = IncomeType::query();

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
            $income_types = $query->paginate($item_per_page)->appends($data);
            $income_types->pagination_summary = get_pagination_summary($income_types);
        } else {
            $income_types = $query->get();
        }

        return $income_types;
    }

    public function create(array $data)
    {
        return IncomeType::create($data);
    }

    public function update(IncomeType $model, array $data)
    {
        return $model->update($data);
    }

    public function delete(IncomeType $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return IncomeType::findOrFail($id);
    }
}
