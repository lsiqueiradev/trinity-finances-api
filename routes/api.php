<?php

use App\Http\Controllers\AccountArchivedController;
use App\Http\Controllers\AccountBalanceController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\CategoryArchivedController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{categoryId}', [CategoryController::class, 'show'])->name('categories.show');
    Route::patch('/categories/{categoryId}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{categoryId}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::patch('/categories/{categoryId}/archive', [CategoryArchivedController::class, 'store'])->name('categories.archive');
    Route::patch('/categories/{categoryId}/unarchive', [CategoryArchivedController::class, 'destroy'])->name('categories.unarchive');

    // accounts
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::put('/accounts/{accountId}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{accountId}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::put('/accounts/{accountId}/balance', [AccountBalanceController::class, 'update'])->name('accounts.balance.update');
    Route::post('/accounts/{accountId}/archive', [AccountArchivedController::class, 'store'])->name('accounts.archive');

    // transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transactionId}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::put('/transactions/{transactionId}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transactionId}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // balances
    Route::get('/balances/{balanceType}', [BalanceController::class, 'index'])->name('balances.index');

    // institutions
    Route::get('/institutions', [InstitutionController::class, 'index'])->name('institutions.index');

    // objectives
    Route::get('/objectives', [ObjectiveController::class, 'index'])->name('objectives.index');
    Route::post('/objectives', [ObjectiveController::class, 'store'])->name('objectives.store');
    Route::put('/objectives/{objectiveId}', [ObjectiveController::class, 'update'])->name('objectives.update');
    Route::delete('/objectives/{objectiveId}', [ObjectiveController::class, 'destroy'])->name('objectives.destroy');

    // user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});

require __DIR__ . '/auth.php';
