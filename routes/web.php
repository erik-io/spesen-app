<?php

declare(strict_types=1);

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseManagementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('supervisor')) {
        return redirect()->route('expenses.management.index');
    }

    if ($user->hasRole('employee')) {
        return redirect()->route('expenses.index');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])
    ->prefix('expenses')
    ->name('expenses.')
    ->group(function () {

        Route::middleware(['role:employee'])->group(function () {
            Route::get('/', [ExpenseController::class, 'index'])->name('index');

            // Form for creating a new expense
            Route::get('/create', [ExpenseController::class, 'create'])->name('create');

            // Save the new expense
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
        });

        Route::middleware(['role:supervisor'])
            ->prefix('management')
            ->name('management.')
            ->group(function () {
                // Custom route for the supervisor dashboard
                Route::get('/', [ExpenseManagementController::class, 'index'])->name('index');

                // View all expenses in the system
                Route::get('/all', [ExpenseManagementController::class, 'history'])->name('history');

                // Detailed view of an expense for review
                Route::get('/{expense}', [ExpenseManagementController::class, 'show'])->name('show');

                // Approving or rejecting an expense
                Route::patch('/{expense}/approve', [ExpenseManagementController::class, 'approve'])->name('approve');
                Route::patch('/{expense}/reject', [ExpenseManagementController::class, 'reject'])->name('reject');
            });
    });

require __DIR__ . '/auth.php';
