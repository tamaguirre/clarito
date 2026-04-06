<?php

use Illuminate\Support\Facades\Route;

Route::view('/register', 'register')->name('register');
Route::view('/login', 'login')->name('login');
Route::view('/upload', 'upload')->name('upload');
Route::get('/resume/{token}', function (string $token) {
    return view('resume', ['uploadToken' => $token]);
})->name('resume');
Route::view('/documents', 'documents')->name('documents');
Route::view('/profile', 'profile')->name('profile');
Route::view('/admin/users', 'admin.users')->name('admin.users');
Route::view('/admin/companies', 'admin.companies')->name('admin.companies');
