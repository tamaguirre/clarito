<?php

use Illuminate\Support\Facades\Route;


Route::view('/register', 'register')->name('register');
Route::view('/login', 'login')->name('login');
Route::view('/upload', 'upload')->name('upload');
Route::view('/resume', 'resume')->name('resume');
Route::view('/documents', 'documents')->name('documents');
Route::view('/profile', 'profile')->name('profile');
