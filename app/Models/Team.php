<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Team extends Model
{
    use SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id');
    }
}
