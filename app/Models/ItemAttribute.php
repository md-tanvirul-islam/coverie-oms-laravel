<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemAttribute extends Model
{
    use SoftDeletes, HasTeamScope;

    protected $fillable = [
        'team_id',
        'store_id',
        'item_id',
        'key',
        'label',
        'type',
        'options',
        'is_required',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
