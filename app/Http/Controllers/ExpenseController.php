<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function store(StoreExpenseRequest $request)
    {
        auth()->user()->expenses()->create($request->validated());
        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }

    public function create(): View
    {
        return view('expenses.create');
    }

    public function index(): View
    {
        $user = auth()->user();

        $expenses = $user->expenses()->latest()->paginate(10);

        return view('expenses.index', ['expenses' => $expenses]);
    }
}
