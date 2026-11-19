<?php

use App\Http\Controllers\HRM\AttendanceController;
use App\Http\Controllers\HRM\DashboardController;
use App\Http\Controllers\HRM\DepartmentController;
use App\Http\Controllers\HRM\EmployeeController;
use App\Http\Controllers\HRM\LeaveController;
use App\Http\Controllers\HRM\PayrollController;
use App\Http\Controllers\HRM\PayslipController;
use App\Http\Controllers\HRM\PositionController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('employees', EmployeeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('leaves', LeaveController::class)->parameters(['leaves' => 'leave']);
    Route::resource('payrolls', PayrollController::class);
    Route::resource('payslips', PayslipController::class);
});

require __DIR__.'/settings.php';
