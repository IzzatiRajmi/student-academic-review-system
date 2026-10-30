<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProcessRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ReviewRequestController;
use Illuminate\Support\Facades\Route;

// ---------- Guest ----------
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Student ----------
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('/requests', [ReviewRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [ReviewRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [ReviewRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}/edit', [ReviewRequestController::class, 'edit'])->whereNumber('id')->name('requests.edit');
    Route::put('/requests/{id}', [ReviewRequestController::class, 'update'])->whereNumber('id')->name('requests.update');
    Route::delete('/requests/{id}', [ReviewRequestController::class, 'destroy'])->whereNumber('id')->name('requests.destroy');
});

// ---------- Administrator ----------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/requests/pending', [ProcessRequestController::class, 'pending'])->name('requests.pending');
    Route::get('/requests', [ProcessRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{reviewRequest}/process', [ProcessRequestController::class, 'edit'])->name('requests.process');
    Route::post('/requests/{reviewRequest}/process', [ProcessRequestController::class, 'update'])->name('requests.decide');
});
