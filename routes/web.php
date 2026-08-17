<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\ClubMemberController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController as AdminEventController;

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

// Routes cho Manager (Quản lý Check-in) — chỉ admin và club_manager mới được vào
Route::middleware(['auth', 'role:admin,club_manager'])->group(function () {
    Route::get('/manager/checkin', [App\Http\Controllers\Manager\CheckinController::class, 'index'])->name('manager.checkin.index');
    Route::post('/manager/checkin/process', [App\Http\Controllers\Manager\CheckinController::class, 'process'])->name('manager.checkin.process');
});

// Routes dùng chung cho Admin và Club Manager (Quản lý Sự kiện)
Route::middleware(['auth', 'role:admin,club_manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('events', AdminEventController::class);
    Route::patch('events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve');
    Route::patch('events/{event}/reject', [AdminEventController::class, 'reject'])->name('events.reject');
});

// Routes dành cho Admin (Quản lý CLB, Thành viên CLB, Danh mục sự kiện, Người dùng)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('clubs', ClubController::class);
    Route::post('clubs/{club}/members', [ClubMemberController::class, 'store'])->name('clubs.members.store');
    Route::put('clubs/{club}/members/{member}', [ClubMemberController::class, 'update'])->name('clubs.members.update');
    Route::delete('clubs/{club}/members/{member}', [ClubMemberController::class, 'destroy'])->name('clubs.members.destroy');

    // Fix 4: Thêm except(['show']) — không có view show cho categories
    Route::resource('categories', EventCategoryController::class)->except(['show']);

    // Fix 2: Thêm except(['create', 'store', 'show']) — không có view show cho users
    Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
});
