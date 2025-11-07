<?php

namespace App\Http\Requests;

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
            'amount' => 'required|decimal:2|min:0.01|max:99999999.99',
            'expense_date' => 'required|date|before_or_equal:today|after_or_equal:' . now()->subDays(90)->toDateString(),
            'cost_center' => 'required|string|max:50',
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
            $amount = str_replace(',', '.', $amount);

            // Check if it's numeric and appears to be an integer (no decimal point)
            if (is_numeric($amount) && strpos($amount, '.') === false) {
                // Append '.00'
                $amount .= '.00';
            }

            // Merge the manipulated value back into the request
            $this->merge([
                'amount' => $amount,
            ]);
        }
    }
}
