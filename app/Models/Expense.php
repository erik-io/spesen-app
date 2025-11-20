<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'expense_date',
        'cost_center',
        'status',
        'rejection_comment',
    ];

    protected $casts = [
        'expense_date' => 'datetime:Y-m-d',
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
