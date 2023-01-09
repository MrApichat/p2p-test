<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'merchant_order_id',
        'amount', // coin amount
        'total_price',
        'status',
        'payment_method_id'
    ];

    public function merchant_order()
    {
        return $this->belongsTo(MerchantOrder::class, 'merchant_order_id', 'id');
    }

    public function payment_method() {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }
}
