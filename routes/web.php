<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::resource('courses', CourseController::class);
    Route::get('courses/datatable', [CourseController::class, 'dataTable'])->name('courses.data');

});


