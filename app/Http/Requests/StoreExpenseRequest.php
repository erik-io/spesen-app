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
            'expense_date' => 'required|date|before_or_equal:today',
            'cost_center' => 'required|string|max:50',
        ];
    }

    /**
     * Prepare the data for validation.
     * Converts comma to period for amount, to ensure that API clients (e.g Postman) can also send 'de-DE' formats
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => str_replace(',', '.', $this->input('amount')),
            ]);
        }
    }
}
