<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierPaidInvoice extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'courier_name',
        'consignment_id',
        'created_date',
        'invoice_type',
        'collected_amount',
        'recipient_name',
        'recipient_phone',
        'collectable_amount',
        'cod_fee',
        'delivery_fee',
        'final_fee',
        'discount',
        'additional_charge',
        'compensation_cost',
        'promo_discount',
        'payout',
        'merchant_order_id',
        'order_id',
    ];

    protected $casts = [
        'created_date' => 'datetime',
        'collected_amount'     => 'decimal:2',
        'collectable_amount'   => 'decimal:2',
        'cod_fee'              => 'decimal:2',
        'delivery_fee'         => 'decimal:2',
        'final_fee'            => 'decimal:2',
        'discount'             => 'decimal:2',
        'additional_charge'    => 'decimal:2',
        'compensation_cost'    => 'decimal:2',
        'promo_discount'       => 'decimal:2',
        'payout'               => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
