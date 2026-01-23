<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bid;
use App\Models\JobAssignment;

class TaskJob extends Model
{
    protected $table = 'task_jobs';

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'city',//
        'budget',//
        'category_id',
        'status'//
    ];

      public function bids()
    {
        return $this->hasMany(Bid::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 任务所属类别
    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function assignment()
    {
        return $this->hasOne(JobAssignment::class, 'job_id');
    }

    public function photos()
    {
        return $this->hasMany(\App\Models\TaskPhoto::class, 'task_job_id')
            ->orderBy('sort')->orderBy('id');
    }

}

