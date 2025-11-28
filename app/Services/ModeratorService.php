<?php 

namespace App\Services;

use App\Models\Moderator;

class ModeratorService
{
    public function create(array $data)
    {
        return Moderator::create($data);
    }

    public function update(Moderator $moderator, array $data)
    {
        return $moderator->update($data);
    }

    public function delete(Moderator $moderator)
    {
        return $moderator->delete();
    }

    public function dropdown()
    {
        return Moderator::pluck('name', 'id')->toArray();
    }
}
