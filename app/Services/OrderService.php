<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TaskJob;
use InvalidArgumentException;

class OrderService
{

    public function createFromTask(TaskJob $task)
    {
        return Order::create([
            'task_job_id' => $task->id,
            'employer_id' => $task->user_id,
            'worker_id' => $task->worker_id ?? null,
            'amount' => $task->budget ?? 0,
            'service_type' => 'airport',

            'pickup_address' => $task->pickup_address ?? null,
            'dropoff_address' => $task->dropoff_address ?? null,
            'scheduled_at' => $task->scheduled_at ?? null,

            'passengers' => $task->passengers ?? null,
            'luggage' => $task->luggage ?? null,
            'status' => 'pending',
        ]);
    }

    
}