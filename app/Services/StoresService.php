<?php

namespace App\Services;

use App\Models\Stores;

class StoresService
{
    public function create(array $data)
    {
        return Stores::create($data);
    }

    public function update(Stores $model, array $data)
    {
        return $model->update($data);
    }

    public function delete(Stores $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return Stores::findOrFail($id);
    }
}
