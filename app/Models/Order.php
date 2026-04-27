<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'task_job_id',
        'employer_id',
        'service_type',
        'pickup_address',
        'dropoff_address',
        'scheduled_at',
        'passengers',
        'luggage',
        'worker_id',
        'amount',
        'status',
    ];

    /*
    |--------------------------------
    | 关系：订单属于用户
    |--------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------
    | 关系：订单有一个支付记录
    |--------------------------------
    */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /*
    |--------------------------------
    | 状态判断（方便Blade）
    |--------------------------------
    */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function task()
    {
        return $this->belongsTo(TaskJob::class);
    }
}
