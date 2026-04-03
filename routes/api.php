<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\Api\EducationLevelController;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)->name('api.register');
Route::get('/education-levels', [EducationLevelController::class, 'index'])->name('api.education-levels.index');
Route::get('/conditions', [ConditionController::class, 'index'])->name('api.conditions.index');
