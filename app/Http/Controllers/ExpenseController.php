<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * Handle the storage of a new expense for the authenticated user.
     *
     * @param StoreExpenseRequest $request Request instance containing validated data for creating the expense.
     * @return RedirectResponse Redirect response to the expenses index with a success message.
     */
    public function store(StoreExpenseRequest $request)
    {
        auth()->user()->expenses()->create($request->validated());
        return redirect()->route('expenses.index')->with('success', __('messages.feedback.success'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('expenses.create');
    }

    /**
     * Display a paginated list of the authenticated user's expenses, with sorting and filter options.
     *
     * @param Request $request Request instance containing query parameters for sorting, filtering, and pagination.
     * @return View The view displaying the user's expenses.
     */
    public function index(Request $request): View
    {
        // Define allowed sorts
        $allowedSortBy = ['created_at', 'expense_date', 'cost_center', 'amount', 'status'];
        // Define allowed per pages
        $allowedPerPages = [10, 25, 50, 100];

        // Validate sort_by parameter
        $sortBy = $request->query('sort_by', 'created_at'); // Default sort column
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }

        // Validate sort_direction parameter
        $sortDirection = $request->query('sort_direction', 'desc'); // Default sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Validate per_page parameter
        $perPage = (int)$request->query('per_page', 10); // Default per page
        if (!in_array($perPage, $allowedPerPages)) {
            $perPage = 10;
        }

        $user = auth()->user();

        $expenses = $user->expenses()
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return view('expenses.index', compact('expenses'));
    }
}
