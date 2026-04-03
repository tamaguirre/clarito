<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CatalogController;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)->name('api.register');
Route::get('/education-levels', [CatalogController::class, 'educationLevels'])->name('api.education-levels.index');
Route::get('/conditions', [CatalogController::class, 'conditions'])->name('api.conditions.index');
