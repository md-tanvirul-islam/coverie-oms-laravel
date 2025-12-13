<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stores extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'type',
        'logo',
        'status',
        'created_by',
        'updated_by',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
