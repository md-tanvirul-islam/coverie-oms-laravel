<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, HasTeamScope;

    protected $fillable = [
        'team_id',
        'store_id',
        'invoice_code',
        'taker_employee_id',
        'order_date',
        'customer_name',
        'customer_phone',
        'customer_address',
        'total_quantity',
        'sub_total',
        'discount',
        'total_cost'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function taker()
    {
        return $this->belongsTo(Employee::class, 'taker_employee_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
