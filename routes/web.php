<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Routes cho Sinh viên
Route::middleware(['auth'])->group(function () {
    Route::get('/student/events', [App\Http\Controllers\Student\EventController::class, 'index'])->name('student.events.index');
    Route::get('/student/my-tickets', [App\Http\Controllers\Student\EventController::class, 'myTickets'])->name('student.my_tickets');
    Route::post('/student/events/{id}/register', [App\Http\Controllers\Student\EventController::class, 'register'])->name('student.events.register');
    Route::get('/student/events/{id}/certificate', [App\Http\Controllers\Student\EventController::class, 'downloadCertificate'])->name('student.events.certificate');
});

// Routes cho Manager (Quản lý Check-in)
Route::middleware(['auth'])->group(function () {
    Route::get('/manager/checkin', [App\Http\Controllers\Manager\CheckinController::class, 'index'])->name('manager.checkin.index');
    Route::post('/manager/checkin/process', [App\Http\Controllers\Manager\CheckinController::class, 'process'])->name('manager.checkin.process');
});
