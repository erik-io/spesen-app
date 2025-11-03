<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpenseManagementController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::with('user')->where('status', 'pending')->latest()->paginate(10);

        return view('expenses.management.index', ['expenses' => $expenses]);
    }

    public function show(Expense $expense): View
    {
        $expense->load('user');

        return view('expenses.management.show', ['expense' => $expense]);
    }

    public function approve(Expense $expense): RedirectResponse
    {
        $expense->update(['status' => 'approved', 'rejection_comment' => null]); // Clear any previous rejection comment

        return redirect()->route('expenses.management.index')->with('success', 'Expense approved successfully.');
    }

    public function reject(RejectExpenseRequest $request, Expense $expense)
    {
        $expense->update(['status' => 'rejected', 'rejection_comment' => $request->validated('rejection_comment')]);

        return redirect()->route('expenses.management.index')->with('success', 'Expense rejected successfully.');
    }
}
