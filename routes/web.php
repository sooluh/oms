<?php

use App\Http\Controllers\PublicServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicServiceController::class, 'index'])->name('home');

Route::get('/services/{slug}', [PublicServiceController::class, 'show'])->name('services.show');
