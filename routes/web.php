<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\MessageController;
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
    Route::resource('projects', ProjectController::class);

    // Rute untuk tugas
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Rute untuk file (attachment)
    Route::post('projects/{project}/attachments', [AttachmentController::class, 'store'])->name('projects.attachments.store');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');    

    // Rute untuk pesan (diskusi)
    Route::post('projects/{project}/messages', [MessageController::class, 'store'])->name('projects.messages.store');

    // Rute untuk Detail Tugas
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');

    // Rute untuk fungsionalitas di dalam tugas
    Route::post('tasks/{task}/attachments', [AttachmentController::class, 'storeForTask'])->name('tasks.attachments.store');
    Route::post('tasks/{task}/messages', [MessageController::class, 'storeForTask'])->name('tasks.messages.store');

});

// Route autentikasi bawaan Breeze
require __DIR__.'/auth.php';
