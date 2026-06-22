<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'demo/reset', [DemoController::class, 'reset'])->name('demo.reset');
