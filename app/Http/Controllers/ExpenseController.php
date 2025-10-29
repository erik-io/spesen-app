<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function create(): View
    {
        return view('expenses.create');
    }
}
