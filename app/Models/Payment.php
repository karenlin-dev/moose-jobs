<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'amount',
        'status',
        'transaction_id',
    ];

    /*
    |--------------------------------
    | 关系：支付属于订单
    |--------------------------------
    */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------
    | 状态判断
    |--------------------------------
    */
    public function isSuccess()
    {
        return $this->status === 'success';
    }
}
