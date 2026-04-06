<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyRegistrationController;
use App\Http\Controllers\Api\CompanySettingsController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\Api\EducationLevelController;
use App\Http\Controllers\Api\ResumeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->name('api.login');
Route::post('/register', RegisterController::class)->name('api.register');
Route::get('/education-levels', [EducationLevelController::class, 'index'])->name('api.education-levels.index');
Route::get('/conditions', [ConditionController::class, 'index'])->name('api.conditions.index');
Route::get('/company-registrations/{token}', [CompanyRegistrationController::class, 'show'])->name('api.company-registrations.show');
Route::post('/company-registrations/{token}/complete', [CompanyRegistrationController::class, 'complete'])->name('api.company-registrations.complete');

Route::middleware('auth:api')->group(function () {
    Route::get('/resumes', [ResumeController::class, 'index'])->name('api.resumes.index');
    Route::post('/resumes', [ResumeController::class, 'store'])->name('api.resumes.store');
    Route::get('/resumes/{token}', [ResumeController::class, 'show'])->name('api.resumes.show');
    Route::patch('/resumes/{token}', [ResumeController::class, 'update'])->name('api.resumes.update');
});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('api.admin.users.index');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('api.admin.users.update');
    Route::get('/roles', [UserController::class, 'roles'])->name('api.admin.roles.index');

    Route::get('/companies', [CompanyController::class, 'index'])->name('api.admin.companies.index');
    Route::post('/companies', [CompanyController::class, 'store'])->name('api.admin.companies.store');
    Route::patch('/companies/{company}', [CompanyController::class, 'update'])->name('api.admin.companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('api.admin.companies.destroy');
});

Route::middleware(['auth:api', 'company'])->prefix('company')->group(function () {
    Route::get('/catalogs', [CompanySettingsController::class, 'catalogs'])->name('api.company.catalogs');

    Route::get('/configs/{environment}', [CompanySettingsController::class, 'show'])->name('api.company.configs.show');
    Route::put('/configs/{environment}', [CompanySettingsController::class, 'update'])->name('api.company.configs.update');

    Route::get('/webhooks/{environment}', [CompanySettingsController::class, 'webhooks'])->name('api.company.webhooks.index');
    Route::post('/webhooks/{environment}', [CompanySettingsController::class, 'storeWebhook'])->name('api.company.webhooks.store');
    Route::delete('/webhooks/{environment}/{webhook}', [CompanySettingsController::class, 'destroyWebhook'])->name('api.company.webhooks.destroy');
});
