<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Authorization is already handled by the middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'decimal:' . Expense::AMOUNT_SCALE,
                'min:0.01',
                'max:' . pow(10, Expense::AMOUNT_PRECISION - Expense::AMOUNT_SCALE) - 0.01,
            ],
            'expense_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . now()->subDays(Expense::MAX_SUBMISSION_AGE_DAYS)->toDateString(),
            ],
            'cost_center' => [
                'required',
                'string',
                'max:' . Expense::MAX_COST_CENTER_LENGTH,
            ],
            'status' => 'prohibited',
            'user_id' => 'prohibited',
        ];
    }

    /**
     * Prepare the data for validation.
     * Converts comma to period for amount, to ensure that API clients (e.g Postman) can also send 'de-DE' formats
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            // Get the input value
            $amount = $this->input('amount');

            // Convert comma to period
            $amount = str_replace(',', '.', (string)$amount);

            // Format the number to 2 decimal places
            // This fixes the issue where a number failed validation because of a missing decimal place
            if (is_numeric($amount)) {
                $amount = number_format((float)$amount, Expense::AMOUNT_SCALE, '.', '');
            }

            // Merge the manipulated value back into the request
            $this->merge([
                'amount' => $amount,
            ]);
        }
    }
}
