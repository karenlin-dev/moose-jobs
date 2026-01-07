<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    // 允许批量赋值的字段
    protected $fillable = [
        'user_id',
        'job_id',
        'status', // 如果你有 status 字段
    ];
    //
     public function job()
    {
        return $this->belongsTo(TaskJob::class, 'job_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
}
