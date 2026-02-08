<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskJobController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use APP\Models\Category;
use App\Http\Controllers\ProfilePhotoController;


// Route::get('/', function () {

//     $workers = DB::table('users')
//         ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
//         ->select(
//             'users.id',
//             'users.name',
//             'users.role',
//             'profiles.avatar',
//             'profiles.city',
//             'profiles.bio'
//         )
//         ->where('users.role', 'worker')
//         ->orderByDesc('users.id')
//         ->limit(12)
//         ->get();

//     return view('home', compact('workers'));
// })->name('home');
Route::get('/', function () {

    $workers = User::with(['profile.categories'])
        ->where('role', 'worker')
        ->latest()
        ->take(12)
        ->get();

    $categories = Category::orderBy('name')->get();

    return view('home', compact('workers', 'categories'));
})->name('home');

/**
 * Public: Worker list
 */
Route::get('/workers', function () {

    $workers = DB::table('users')
        ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
        ->select(
            'users.id',
            'users.name',
            'profiles.avatar',
            'profiles.city',
            'profiles.bio'
        )
        ->where('users.role', 'worker')
        ->orderByDesc('users.id')
        ->paginate(18);

    return view('workers.index', compact('workers'));
})->name('workers.index');



// Route::delete('/workers/photos/{photo}', [ProfilePhotoController::class, 'destroy'])
//     ->name('workers.photos.destroy');
Route::middleware('auth')->group(function() {
    Route::delete('/worker/photos/{photo}', [WorkerController::class, 'destroyPhoto'])->name('workers.photos.destroy');
    Route::post('/worker/photos/reorder', [WorkerController::class, 'reorderPhotos'])->name('workers.photos.reorder');
});

/**
 * Public: Worker detail (雇主/游客都能看)
 * 只保留这一条 show（不要再写 /workers/{id} 闭包）
 */
Route::get('/workers/{worker}', [WorkerController::class, 'show'])
    ->whereNumber('worker')
    ->name('workers.show');

/**
 * Auth+Verified: Worker 自己编辑资料
 */
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/workers/edit', [WorkerController::class, 'edit'])
        ->name('workers.edit');

    Route::post('/workers/update', [WorkerController::class, 'update'])
        ->name('workers.update');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');


// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tasks/create', [TaskJobController::class, 'create'])
        ->name('tasks.create');

    Route::post('/tasks', [TaskJobController::class, 'store'])
        ->name('tasks.store');
});

/**
 * Public (guest can browse)
 */
Route::get('/tasks', [TaskJobController::class, 'index'])->name('tasks.index');
Route::get('/tasks/errands', [TaskJobController::class, 'errands'])
    ->name('tasks.errands');
Route::get('/tasks/{task}', [TaskJobController::class, 'show'])->name('tasks.show'); // 建议加上

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tasks/{task}/edit', [TaskJobController::class, 'edit'])
        ->name('tasks.edit');

    Route::put('/tasks/{task}', [TaskJobController::class, 'update'])
        ->name('tasks.update');
    Route::patch('/tasks/{task}/quick-accept', [TaskJobController::class, 'quickAccept'])
    ->name('tasks.quickAccept');
    Route::patch('/tasks/{task}/complete', [TaskJobController::class, 'complete'])
        ->name('tasks.complete');

    Route::delete('/tasks/photos/{photo}', [TaskJobController::class, 'destroyPhoto'])
        ->name('tasks.photos.destroy');

    Route::post('/tasks/photos/reorder', [TaskJobController::class, 'reorderPhotos'])
        ->name('tasks.photos.reorder');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tasks/{task}/bid', [BidController::class, 'create'])
        ->name('bids.create');

    Route::post('/tasks/{task}/bid', [BidController::class, 'store'])
        ->name('bids.store');

    // 接受投标（Accept）
    Route::patch('/bids/{bid}/accept', [BidController::class, 'accept'])->name('bids.accept');
    // Bid 显示详情
    Route::get('/bids/{bid}', [BidController::class, 'show'])->name('bids.show');

    // Assignment 显示详情 + 完成操作
    Route::get('/assignments/{assignment}', [JobAssignmentController::class, 'show'])->name('assignments.show');
    Route::patch('/assignments/{assignment}/complete', [JobAssignmentController::class, 'complete'])->name('assignments.complete');

});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
