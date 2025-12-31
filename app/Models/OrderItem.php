<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelJutsu\Artifact\Concerns\HasArtifacts;

class OrderItem extends Model
{
    use SoftDeletes, HasTeamScope, HasArtifacts;

    protected $fillable = [
        'team_id',
        'store_id',
        'order_id',
        'item_id',
        'unit_price',
        'quantity',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function documents()
    {
        return $this->manyArtifacts('documents');
    }
}
