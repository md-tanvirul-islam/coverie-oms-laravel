<?php

namespace App\Services;

use App\Models\Item;

class ItemService
{
    public function list($data = [], $is_query_only = false, $is_paginated = true)
    {
        $query = Item::query();

        $query = $query->with([]);

        //filter options
        if (isset($data['team_id'])) {
            $query->where('team_id', $data['team_id']);
        }

        if (isset($data['store_id'])) {
            $query->where('store_id', $data['store_id']);
        }

        if (isset($data['name'])) {
            $query->where('name', $data['name']);
        }

        if (isset($data['code'])) {
            $query->where('code', $data['code']);
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
            $items = $query->paginate($item_per_page)->appends($data);
            $items->pagination_summary = get_pagination_summary($items);
        } else {
            $items = $query->get();
        }

        return $items;
    }

    public function create(array $data)
    {
        return Item::create($data);
    }

    public function update(Item $model, array $data)
    {
        return $model->update($data);
    }

    public function delete(Item $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return Item::findOrFail($id);
    }
}
