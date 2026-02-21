<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePhoto extends Model
{
    protected $fillable = ['profile_id', 'path', 'type', 'sort'];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
