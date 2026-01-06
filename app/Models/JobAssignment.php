<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAssignment extends Model
{
     protected $fillable = [
        'job_id',
        'employer_id',
        'worker_id',
        'agreed_price',
        'started_at',
        'completed_at',
        'updated_at'
    ];

    protected $casts = [
    'started_at' => 'datetime',
    'updated_at' => 'datetime',
    'completed_at'   => 'datetime', // 如果有结束时间
];

     public function task()
    {
        return $this->belongsTo(TaskJob::class, 'id');
    }
    public function employer() { 
        return $this->belongsTo(User::class,'employer_id'); 
    }
    public function worker() { 
        return $this->belongsTo(User::class,'worker_id');
    }

}
