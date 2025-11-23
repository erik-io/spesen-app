<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AcceptExpenseRequest;
use App\Http\Requests\RejectExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseManagementController extends Controller
{
    public function index(Request $request): View
    {
        $expenses = $this->getPaginatedExpenses($request, Expense::STATUS_PENDING);
        return view('expenses.management.index', compact('expenses'));
    }

    private function getPaginatedExpenses(Request $request, string $statusScope)
    {
        $allowedSortBy = ['created_at', 'expense_date', 'cost_center', 'amount', 'status', 'user_id'];
        $allowedPerPage = [10, 25, 50, 100];

        $defaultSortBy = 'created_at';
        $defaultSortDirection = 'asc'; // Default for 'pending' (oldest first)

        if ($statusScope === 'all') {
            // For history, default to descending order
            $defaultSortDirection = 'desc';
        }

        // Validate sort_by parameter
        $sortBy = $request->query('sort_by', $defaultSortBy);
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = $defaultSortBy;
        }

        // Validate sort_direction parameter
        $sortDirection = $request->query('sort_direction', $defaultSortDirection);
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = $defaultSortDirection;
        }

        // Validate per_page parameter
        $perPage = (int)$request->query('per_page', 10);
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $query = Expense::with('user');

        if ($statusScope === Expense::STATUS_PENDING) {
            $query->where('status', Expense::STATUS_PENDING);
        } elseif ($statusScope === 'all') {
            $status = $request->query('status');
            if ($status && in_array($status, Expense::STATUSES)) {
                $query->where('status', $status);
            }
        }

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    public function show(Expense $expense): View
    {
        return view('expenses.management.show', compact('expense'));
    }

    public function approve(AcceptExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $expense->update(['status' => Expense::STATUS_APPROVED, 'rejection_comment' => null]); // Clear any previous rejection comment

        return redirect()->route('expenses.management.index')->with('success', __('Expense approved successfully.'));
    }

    public function reject(RejectExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $expense->update(['status' => Expense::STATUS_REJECTED, 'rejection_comment' => $request->validated('rejection_comment')]);

        return redirect()->route('expenses.management.index')->with('success', __('Expense rejected successfully.'));
    }

    public function history(Request $request): View
    {
        $expenses = $this->getPaginatedExpenses($request, 'all');
        return view('expenses.management.history', compact('expenses'));
    }
}
