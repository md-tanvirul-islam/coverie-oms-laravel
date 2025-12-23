<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes, HasTeamScope;

    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'phone',
        'joining_date',
        'address',
        'code',
        'commission_fee_per_order',
        'created_by',
        'updated_by'
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

        public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastUpdater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
