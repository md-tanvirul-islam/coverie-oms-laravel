<?php

namespace App\Services;

use App\Models\Store;

class StoreService
{
    public function list($data = [], $is_query_only = false, $is_paginated = true)
    {
        $order = "asc";

        $query = Store::query();

        $query = $query->with([]);

        //filter options
        if (isset($data['user_id'])) {
            $query->where('user_id', $data['user_id']);
        }

        if (isset($data['name'])) {
            $query->where('name', $data['name']);
        }

        if (isset($data['slug'])) {
            $query->where('slug', $data['slug']);
        }

        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }

        if (array_key_exists('status', $data)) {
            $query->where('status', $data['status']);
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
            $stores = $query->paginate($item_per_page)->appends($data);
            $stores->pagination_summary = get_pagination_summary($stores);
        } else {
            $stores = $query->get();
        }

        return $stores;
    }

    public function create(array $data)
    {
        $store = Store::create($data);

        if (!empty($data['logo']) && $store) {

            $store->logo()->store($data['logo']);
        }

        return $store;
    }

    public function update(Store $store, array $data)
    {
        $data = array_filter($data, fn($value) => ! is_null($value));

        if (!empty($data['logo']) && $store) {

            $store->logo()->store($data['logo']);
        }

        return $store->update($data);
    }

    public function delete(Store $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return Store::findOrFail($id);
    }
}
