<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moderator extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'joining_date',
        'address',
        'code'
    ];
}
