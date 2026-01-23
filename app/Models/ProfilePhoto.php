<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePhoto extends Model
{
    protected $fillable = ['profile_id', 'path', 'sort'];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
