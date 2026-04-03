<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\Api\EducationLevelController;
use App\Http\Controllers\Api\ResumeController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->name('api.login');
Route::post('/register', RegisterController::class)->name('api.register');
Route::get('/education-levels', [EducationLevelController::class, 'index'])->name('api.education-levels.index');
Route::get('/conditions', [ConditionController::class, 'index'])->name('api.conditions.index');

Route::middleware('auth:api')->group(function () {
    Route::get('/resumes', [ResumeController::class, 'index'])->name('api.resumes.index');
    Route::post('/resumes', [ResumeController::class, 'store'])->name('api.resumes.store');
    Route::get('/resumes/{token}', [ResumeController::class, 'show'])->name('api.resumes.show');
    Route::patch('/resumes/{token}', [ResumeController::class, 'update'])->name('api.resumes.update');
});
