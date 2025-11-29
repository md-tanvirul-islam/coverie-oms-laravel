<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Moderator extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'joining_date',
        'address',
        'code'
    ];

    /**
     * Accessor: full_identity
     * Output Format: "Name (CODE)"
     */
    protected function nameAndCode(): Attribute
    {
        return Attribute::get(function () {
            return "{$this->name} ({$this->code})";
        });
    }
}
