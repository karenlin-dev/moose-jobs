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

    public function taskJob()
    {
        return $this->belongsTo(TaskJob::class, 'task_job_id');
    }
}