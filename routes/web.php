<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\JobTitleController;
use Illuminate\Support\Facades\Route;

// Halaman utama, arahkan ke login
Route::get('/', function () {
    return view('auth.login');
});

// Halaman dashboard setelah login
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup route yang memerlukan login
Route::middleware('auth')->group(function () {
    // Route untuk profil pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Resource Routes untuk Manajemen ---
    // Menggunakan underscore agar konsisten dengan pemanggilan di view
    Route::resource('users', UserController::class);
    Route::resource('organizations', OrganizationController::class);
    Route::resource('job_titles', JobTitleController::class);
    // Route untuk Project akan kita tambahkan di sini nanti
});

// Route autentikasi bawaan Breeze
require __DIR__.'/auth.php';
