<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskJobController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\WorkerController;

// Route::get('/workers/edit', function () {
//     return 'workers edit works';
// });

Route::get('/health', function () {
    return 'OK';
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// });
// routes/web.php
// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// });
// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
//     // Worker Dashboard（可选独立路由，兼容操作后重定向）
//     Route::get('/worker/dashboard', [DashboardController::class, 'dashboard'])->name('worker.dashboard');
    
// });

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
    Route::get('/tasks', [TaskJobController::class, 'index'])
        ->name('tasks.index');
});

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/tasks/{task}/bid', [BidController::class, 'create'])
//         ->name('bids.create');

//     Route::post('/tasks/{task}/bid', [BidController::class, 'store'])
//         ->name('bids.store');
// });

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

Route::middleware(['auth', 'verified'])->group(function () {

    // Worker 自己编辑（必须在前）
    Route::get('/workers/edit', [WorkerController::class, 'edit'])
        ->name('workers.edit');

    Route::post('/workers/update', [WorkerController::class, 'update'])
        ->name('workers.update');

    // 雇主查看某个 worker
    Route::get('/workers/{worker}', [WorkerController::class, 'show'])
        ->name('workers.show');
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
