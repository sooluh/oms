<?php

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ServiceController::class, 'index'])->name('home');

Route::get('attachments/{path}', [StorageController::class, 'attachments'])
    ->where('path', '.*')
    ->name('attachments.show');

Route::get('{slug}', [ServiceController::class, 'show'])->name('service.show');
Route::post('{slug}/search', [ServiceController::class, 'search'])->name('service.search');
