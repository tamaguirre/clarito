<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\Api\EducationLevelController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->name('api.login');
Route::post('/register', RegisterController::class)->name('api.register');
Route::get('/education-levels', [EducationLevelController::class, 'index'])->name('api.education-levels.index');
Route::get('/conditions', [ConditionController::class, 'index'])->name('api.conditions.index');
Route::post('/uploads', [UploadController::class, 'store'])->middleware('auth:api')->name('api.uploads.store');
Route::get('/uploads/{token}', [UploadController::class, 'show'])->middleware('auth:api')->name('api.uploads.show');
