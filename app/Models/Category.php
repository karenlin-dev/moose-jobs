<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

     protected $fillable = ['name', 'color', 'icon', 'slug', 'description'];

     public static function iconMap()
    {
        return [
            'moving' => '🚚',
            'cleaning' => '🧹',
            'maintenance' => '🔧',
            'renovation' => '🏠',
            'heat' => '🔥',
            'yard' => '🌿',
            'delivery' => '📦',
            'baking' => '🎂',
            'daycare' => '👶',
            'wellness' => '💆',
            'web-design' => '💻',
        ];
    }                   
    public function jobs() {
        return $this->hasMany(TaskJob::class);
    }

    public function profiles()
    {
        return $this->belongsToMany(\App\Models\Profile::class, 'category_profile')
            ->withTimestamps();
    }

    public function getColorKeyAttribute()
    {
        return $this->color ?: 'gray';
    }

    public function getColorClassesAttribute()
    {
        return match ($this->color_key) {
            'red' => [
                'bg' => 'bg-red-100',
                'text' => 'text-red-700',
                'dot' => 'bg-red-500',
            ],
            'green' => [
                'bg' => 'bg-green-100',
                'text' => 'text-green-700',
                'dot' => 'bg-green-500',
            ],
            'blue' => [
                'bg' => 'bg-blue-100',
                'text' => 'text-blue-700',
                'dot' => 'bg-blue-500',
            ],
            'yellow' => [
                'bg' => 'bg-yellow-100',
                'text' => 'text-yellow-700',
                'dot' => 'bg-yellow-500',
            ],
            'purple' => [
                'bg' => 'bg-purple-100',
                'text' => 'text-purple-700',
                'dot' => 'bg-purple-500',
            ],
            'pink' => [
                'bg' => 'bg-pink-100',
                'text' => 'text-pink-700',
                'dot' => 'bg-pink-500',
            ],
            'teal' => [
                'bg' => 'bg-teal-100',
                'text' => 'text-teal-700',
                'dot' => 'bg-teal-500',
            ],
            default => [
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-700',
                'dot' => 'bg-gray-400',
            ],
        };
    }

    public function getIconEmojiAttribute()
    {
        return [
            'moving' => '🚚',
            'cleaning' => '🧹',
            'maintenance' => '🔧',
            'renovation' => '🏠',
            'heat' => '🔥',
            'yard' => '🌿',
            'delivery' => '📦',
            'baking' => '🎂',
            'rental' => '🏡',
            'daycare' => '👶',
            'wellness' => '💆',
            'web-design' => '💻',
        ][$this->icon] ?? '📦';
    }
}
