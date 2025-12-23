<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, HasTeamScope;

    protected $fillable = [
        'store_id',
        'invoice_id',
        'order_date',
        'customer_name',
        'customer_phone',
        'customer_address',
        'total_cost',
        'phone_model',
        'employee_id',
        'quantity',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
