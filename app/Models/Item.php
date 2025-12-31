<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes, HasTeamScope;

    protected $fillable = [
        'team_id',
        'store_id',
        'name',
        'code',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function attributes()
    {
        return $this->hasMany(ItemAttribute::class, 'item_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
