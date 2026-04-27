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
        'pickup_address',
        'dropoff_address',
        'distance_km',
        'weight_kg',
        'size_level',
        'status',
        'service_type',
        'pickup_time',
        'worker_id',
        'can_accept',
        'scheduled_at',
        'passengers',
        'luggage',
        'payment_status',
        'task_type',
        'order_id'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'pickup_time' => 'datetime',
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

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function isPending()
    {
        return $this->status === 'OPEN';
    }

    public function isAccepted()
    {
        return $this->status === 'IN_PROGRESS';
    }

    public function isCompleted()
    {
        return $this->status === 'COMPLETED';
    }

    public function isBidding(): bool
    {
        return $this->task_type === 'bidding';
    }

    public function isInstant(): bool
    {
        return $this->task_type === 'instant';
    }

    public function isAirport(): bool
    {
        return $this->service_type === 'airport';
    }

    public function isOwner(): bool
    {
        return auth()->id() === $this->user_id;
    }

    public function canHaveBids(): bool
    {
        return $this->task_type === 'bidding';
    }

    public function hasPendingBids(): bool
    {
        return $this->bids()->where('status', 'pending')->exists();
    }

    public function canBeEdited(): bool
    {
        return $this->isOwner() && $this->status !== 'completed';
    }

    public static function resolveTaskType(?string $serviceType): string
    {
        return $serviceType === 'airport'
            ? 'instant'
            : 'bidding';
    }

    public function taskType(): string
    {
        return self::resolveTaskType($this->service_type);
    }

    public function getIsInstantAttribute(): bool
    {
        return $this->taskType() === 'instant';
    }

    public function canBeAccepted(): bool
    {
        if ($this->user_id) {
            return false;
        }

        if ($this->isInstant()) {
            if ($this->isAirport() && !$this->pickup_time) {
                return false;
            }

            if ($this->pickup_time && now()->diffInHours($this->pickup_time, false) < 2) {
                return false;
            }
        }

        return true;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

