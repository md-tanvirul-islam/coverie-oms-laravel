<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'invoice_id',
        'order_date',
        'customer_name',
        'customer_phone',
        'customer_address',
        'total_cost',
        'phone_model',
        'moderator_id',
        'quantity',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function moderator()
    {
        return $this->belongsTo(Moderator::class);
    }
}
