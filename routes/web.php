<?php

use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ServiceController::class, 'index'])->name('home');

Route::get('{slug}', [ServiceController::class, 'show'])->name('service.show');
Route::post('{slug}/search', [ServiceController::class, 'search'])->name('service.search');
