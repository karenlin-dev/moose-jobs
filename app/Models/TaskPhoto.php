<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPhoto extends Model
{
    protected $fillable = [
        'task_job_id',
        'path',
        'sort',
    ];

    public function task()
    {
        return $this->belongsTo(TaskJob::class, 'id');
    }
}