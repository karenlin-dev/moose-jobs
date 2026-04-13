<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
       $workers = User::with(['profile.categories'])
            ->where('role', 'worker')
            ->orderBy('created_at', 'asc')
            ->take(12)
            ->get();

        $categories = Category::orderBy('name')->get();

        $announcements = Announcement::latest()
            ->take(5)
            ->get();

        return view('home', compact(
            'workers',
            'categories',
            'announcements'
        ));
    }
}