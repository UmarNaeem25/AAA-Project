<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReportController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/resources', [CalendarController::class, 'resources'])->name('calendar.resources');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/shifts/create', [CalendarController::class, 'create'])->name('shifts.create');
    Route::post('/shifts', [CalendarController::class, 'store'])->name('shifts.store');
    Route::post('/calendar/update-shift/{shift}', [CalendarController::class, 'updateShift'])->name('calendar.update-shift');
    Route::post('/calendar/assign-all-open-shifts', [CalendarController::class, 'assignAllOpenShifts'])
    ->name('calendar.assignAllOpenShifts');

    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('/locations/search', [LocationController::class, 'search'])->name('locations.search');
    Route::get('/roles/search', [RoleController::class, 'search'])->name('roles.search');
    
    Route::resource('roles', RoleController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('users', UserController::class);

    Route::get('/download-algorithm-report', [ReportController::class, 'downloadAlgorithmReport']);
});