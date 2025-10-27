<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:employee'])->prefix('/expenses')->group(function () {
    // Dashboard for employees (status of their own expenses)
    Route::get('/', [ExpenseController::class, 'index'])->name('expenses.index');

    // Form for creating a new expense
    Route::get('/create', [ExpenseController::class, 'create'])->name('expenses.create');

    // Save the new expense
    Route::post('', [ExpenseController::class, 'store'])->name('expenses.store');
});

Route::middleware(['auth', 'role:supervisor'])->prefix('/expenses/management')->name('admin.')->group(function () {
    // Custom route for the supervisor dashboard
    Route::get('/', [ExpenseController::class, 'adminIndex'])->name('expenses.index');

    // Detailed view of an expense for review
    Route::get('/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');

    // Approving or rejecting an expense
    Route::patch('/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::patch('/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
});

require __DIR__ . '/auth.php';
