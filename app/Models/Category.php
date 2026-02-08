<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

     protected $fillable = ['name', 'slug', 'description'];

    public function jobs() {
        return $this->hasMany(TaskJob::class);
    }

    public function profiles()
    {
        return $this->belongsToMany(\App\Models\Profile::class, 'category_profile')
            ->withTimestamps();
    }

}
