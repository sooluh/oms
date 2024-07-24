<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', fn () => redirect(route('filament.admin.auth.login')))->name('login');
