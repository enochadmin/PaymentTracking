<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ContractController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource routes for application models
    Route::resource('users', UserController::class)->middleware('module.access:users');
    Route::resource('suppliers', SupplierController::class)->middleware('module.access:suppliers');
    Route::resource('projects', ProjectController::class)->middleware('module.access:projects');
    Route::resource('banks', \App\Http\Controllers\BankController::class)->middleware('module.access:banks');
    Route::resource('teams', \App\Http\Controllers\TeamController::class)->middleware('module.access:teams');
    Route::resource('payment-requests', \App\Http\Controllers\PaymentRequestController::class);
    
    // Contract routes
    Route::resource('contracts', ContractController::class);
    Route::get('/payment-requests/{paymentRequest}/create-contract', [ContractController::class, 'createFromPaymentRequest'])
        ->name('contracts.createFromPaymentRequest');
    
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.members.add')->middleware('module.access:teams');
    Route::get('/teams/{team}/add-members', [TeamController::class, 'addMembersForm'])->name('teams.members.form')->middleware('module.access:teams');
    Route::post('/banks/{bank}/toggle-active', [\App\Http\Controllers\BankController::class, 'toggleActive'])->name('banks.toggle-active')->middleware('module.access:banks');
});

require __DIR__.'/auth.php';
