<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'coin_id',
        'receiver_id',
        'amount',
        'status'
    ];

    public function coin()
    {
        return $this->belongsTo(Currency::class, 'coin_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}
