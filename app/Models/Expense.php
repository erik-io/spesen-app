<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'amount',
        'expense_date',
        'cost_center',
    ];

    protected $casts = [
        'expense_date' => 'datetime',
    ];

    /**
     * Get the user that owns the expense.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // $expense->user
    }
}
