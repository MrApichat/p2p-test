<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'currency',
        'total',
        'in_order',
        'user_id',
        'coin_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function coin()
    {
        return $this->belongsTo(Currency::class,'coin_id','id');
    }
}
