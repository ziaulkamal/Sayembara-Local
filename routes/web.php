<?php

use App\Http\Controllers\PartisipanController;
use Illuminate\Support\Facades\Route;

// route::get('/', function () {
//     return redirect()->route('login');
// })->name('home');
Route::get('/login', function () {
    return view('form.login');
})->name('login');

Route::prefix('daftar')->group(function () {
    Route::get('/', [PartisipanController::class, 'register'])->name('register');
    Route::post('/', [PartisipanController::class, 'store'])->name('register.store');
});

Route::get('/login', [PartisipanController::class, 'login'])->name('login');
Route::post('/send-otp', [PartisipanController::class, 'sendOtp'])
    ->middleware('throttle:otp')
    ->name('send.otp');

Route::post('/verify-otp', [PartisipanController::class, 'verifyOtp'])
    ->middleware('throttle:otp')
    ->name('verify.otp');

Route::get('/dashboard', [PartisipanController::class, 'dashboard'])->middleware('guest.login')->name('dashboard');

Route::get('/dashboard/upload', function () {
    return view('dashboard.upload');
})->middleware('guest.login')->name('dashboard.upload.form');

Route::post('/dashboard/upload', [\App\Http\Controllers\DashboardController::class, 'upload'])->middleware('guest.login')->name('dashboard.upload');
