<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function merchant_orders()
    {
        return $this->belongsToMany(MerchantOrder::class, 'merchant_orders_payment_methods', 'payment_method_id', 'merchant_order_id');
    }
}
