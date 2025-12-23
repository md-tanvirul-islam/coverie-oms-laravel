<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'phone',
        'joining_date',
        'address',
        'code',
        'commission_fee_per_order',
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

    public function user(){
        return $this->belongsTo(User::class);
    }
}
