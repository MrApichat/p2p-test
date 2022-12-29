<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'fiat_id',
        'coin_id',
        'merchant_id',
        'price',
        'available_coin',
        'lower_limit',
        'status'
    ];

    public function fiat()
    {
        return $this->belongsTo(Currency::class, 'fiat_id', 'id');
    }

    public function coin()
    {
        return $this->belongsTo(Currency::class, 'coin_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id', 'id');
    }

    public function payment_methods()
    {
        return $this->belongsToMany(PaymentMethod::class,'merchant_orders_payment_methods','merchant_order_id','payment_method_id');
    }

}
