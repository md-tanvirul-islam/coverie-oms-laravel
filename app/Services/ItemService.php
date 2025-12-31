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
        $item = Item::create($data);

        foreach ($data['item_attributes'] ?? [] as $attr) {
            $attribute = $item->attributes()->create([
                'store_id'    => $data['store_id'],
                'team_id'     => $data['team_id'],
                'label'       => $attr['label'],
                'type'        => $attr['type'],
                'options'     => isset($attr['options'])
                    ? array_map('trim', explode(',', $attr['options']))
                    : null,
                'is_required' => $attr['is_required'] ?? false,
                'sort_order'  => $attr['sort_order'] ?? 0,
            ]);
        }

        return $item;
    }

    public function update(Item $item, array $data)
    {
        $item->update($data);

        $ids = [];

        foreach ($data['item_attributes'] ?? [] as $attr) {

            $payload = [
                'store_id'    => $data['store_id'],
                'team_id'     => $data['team_id'],
                'label'       => $attr['label'],
                'type'        => $attr['type'],
                'options'     => isset($attr['options'])
                    ? array_map('trim', explode(',', $attr['options']))
                    : null,
                'is_required' => $attr['is_required'] ?? false,
                'sort_order'  => $attr['sort_order'] ?? 0,
            ];

            $attribute = $item->attributes()->updateOrCreate(
                ['id' => $attr['id'] ?? null],
                $payload
            );

            $ids[] = $attribute->id;
        }

        // Delete removed attributes
        $item->attributes()
            ->when($ids, fn($q) => $q->whereNotIn('id', $ids))
            ->when(empty($ids), fn($q) => $q)
            ->delete();

        return $item;
    }

    public function delete(Item $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return Item::findOrFail($id);
    }

    public function dropdown()
    {
        return Item::where('is_active', true)->pluck('name', 'id')->toArray();
    }
}
