<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\ClubMemberController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Routes dành cho Admin (Quản lý CLB, Thành viên CLB, Danh mục sự kiện, Người dùng)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('clubs', ClubController::class);
    Route::post('clubs/{club}/members', [ClubMemberController::class, 'store'])->name('clubs.members.store');
    Route::put('clubs/{club}/members/{member}', [ClubMemberController::class, 'update'])->name('clubs.members.update');
    Route::delete('clubs/{club}/members/{member}', [ClubMemberController::class, 'destroy'])->name('clubs.members.destroy');

    Route::resource('categories', EventCategoryController::class);
    Route::resource('users', UserController::class)->except(['create', 'store']);
});