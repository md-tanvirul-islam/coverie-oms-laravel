<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpressType extends Model
{
    use SoftDeletes, HasTeamScope;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
