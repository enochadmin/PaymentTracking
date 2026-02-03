<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PaymentDocumentController;
use App\Http\Controllers\ProcurementReviewController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\FinalPaymentRequestController;
use App\Http\Controllers\SessionController;
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

    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
    // Resource routes for application models
    Route::resource('users', UserController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('projects', ProjectController::class);
    Route::patch('/banks/{bank}/toggle-active', [BankController::class, 'toggleActive'])->name('banks.toggle-active');
    Route::resource('banks', BankController::class);
    
    Route::get('/teams/{team}/members', [TeamController::class, 'addMembersForm'])->name('teams.members.form');
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.members.add');
    Route::resource('teams', TeamController::class);
    
    // Payment Documents (Procurement Officer)
    Route::resource('payment-documents', PaymentDocumentController::class);
    
    // Procurement Review
    Route::prefix('procurement-review')->group(function () {
        Route::get('/', [ProcurementReviewController::class, 'index'])->name('procurement-review.index');
        Route::get('/{paymentDocument}', [ProcurementReviewController::class, 'show'])->name('procurement-review.show');
        Route::post('/{paymentDocument}/approve', [ProcurementReviewController::class, 'approve'])->name('procurement-review.approve');
        Route::post('/{paymentDocument}/reject', [ProcurementReviewController::class, 'reject'])->name('procurement-review.reject');
    });

    // Contracts
    Route::resource('contracts', ContractController::class);
    Route::get('/payment-documents/{paymentDocument}/create-contract', [ContractController::class, 'createFromPaymentDocument'])
        ->name('contracts.createFromPaymentDocument');
        
    // Final Payment Requests (Commercial -> Finance)
    Route::resource('final-payment-requests', FinalPaymentRequestController::class);
    Route::get('/payment-documents/{paymentDocument}/create-final-request', [FinalPaymentRequestController::class, 'createFromDocument'])
        ->name('final-payment-requests.createFromDocument');
        
    // Finance Actions
    Route::post('/final-payment-requests/{finalPaymentRequest}/approve-finance', [FinalPaymentRequestController::class, 'approveFinance'])
        ->name('final-payment-requests.approveFinance');
    Route::post('/final-payment-requests/{finalPaymentRequest}/mark-paid', [FinalPaymentRequestController::class, 'markAsPaid'])
        ->name('final-payment-requests.markPaid');
});

require __DIR__.'/auth.php';
