<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    public const AMOUNT_PRECISION = 10;
    public const AMOUNT_SCALE = 2;
    public const MAX_SUBMISSION_AGE_DAYS = 90;
    public const MAX_COST_CENTER_LENGTH = 50;
    public const MAX_REJECTION_COMMENT_LENGTH = 5000;
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUSES = [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED];

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
