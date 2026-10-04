<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyClassController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\TaxRecordController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SmsReminderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------
// Auth
// ---------------------------------------------------------------
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------------------------------------------------------------
// Everyone logged in (Administrator, Collector, Viewer):
// Dashboard, Owners, Properties, Payments, Receipts
// ("waa in lacagta iyo property/owner-ka ay dadka oo dhan arki karaan")
// ---------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('owners', OwnerController::class);
    Route::resource('properties', PropertyController::class);

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/receipts/{payment}', [ReceiptController::class, 'show'])->name('receipts.show');

    // -------------------------------------------------------------
    // Administrator ONLY: Tax Records, Reports, Activity Logs, Users,
    // SMS Reminders, and Settings (Property Class/Type/Village).
    // Collector/Viewer accounts CANNOT see or open these pages.
    // -------------------------------------------------------------
    Route::middleware('role:Administrator')->group(function () {
        Route::get('/tax-records', [TaxRecordController::class, 'index'])->name('tax-records.index');
        Route::post('/tax-records/generate', [TaxRecordController::class, 'generate'])->name('tax-records.generate');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');

        Route::get('/sms-reminders', [SmsReminderController::class, 'index'])->name('sms-reminders.index');
        Route::post('/sms-reminders/send-individual', [SmsReminderController::class, 'sendIndividual'])->name('sms-reminders.send-individual');
        
        

        // Settings: Property Classes, Property Types, Villages/Neighborhoods
        Route::resource('property-classes', PropertyClassController::class)->except(['create', 'edit', 'show']);
        Route::resource('property-types', PropertyTypeController::class)->except(['create', 'edit', 'show']);
        Route::resource('villages', VillageController::class)->except(['create', 'edit', 'show']);
    });
});
