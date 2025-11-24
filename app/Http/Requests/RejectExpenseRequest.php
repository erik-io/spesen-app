<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RejectExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $expense = $this->route('expense');

        return $this->user()->hasRole('supervisor') && $expense->isPending();
    }

    /**
     * Handle failed authorization for rejecting an expense.
     *
     * @throws Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return void
     */
    public function failedAuthorization()
    {
        $expense = $this->route('expense');

        if ($this->user()->hasRole('supervisor') && !$expense->isPending()) {
            throw new HttpResponseException(
                back()->with('error', __('This expense has already been processed.'))
            );
        }

        // If the user is not authorized, fall back to the default behavior
        parent::failedAuthorization();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'rejection_comment' =>
                [
                    'required',
                    'string',
                    'filled',
                    'max:' . Expense::MAX_REJECTION_COMMENT_LENGTH,
                ],
        ];
    }
}
