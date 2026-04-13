<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'type',
        'is_pinned'
    ];

}