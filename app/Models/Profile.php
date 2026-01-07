<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'avatar', 'bio', 'skills', 'phone', 'rating', 'total_reviews','city'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
