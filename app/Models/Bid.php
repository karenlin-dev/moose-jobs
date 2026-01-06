<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'price',
        'message',
        'status'
    ];

    public function task()
    {
        return $this->belongsTo(TaskJob::class, 'job_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
