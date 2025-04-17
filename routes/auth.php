<?php

use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Auth\SessionEmailController;
use App\Http\Controllers\Auth\SessionPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterUserController::class, 'store'])->name('register');
Route::post('/sessions/password', [SessionPasswordController::class, 'store'])->name('login');
Route::post('/sessions/email', [SessionEmailController::class, 'store'])->name('email');
Route::post('/sessions/logout', [SessionPasswordController::class, 'destroy'])->middleware('auth')->name('logout');
