<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    //use HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isWorker(): bool
    {
        return $this->role === 'worker';
    }

    public function postedTasks() { return $this->hasMany(TaskJob::class, 'user_id'); }
    public function bids() { return $this->hasMany(Bid::class, 'user_id'); }

    public function assignments()
    {
        return $this->hasMany(JobAssignment::class, 'worker_id');
    }
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

}
