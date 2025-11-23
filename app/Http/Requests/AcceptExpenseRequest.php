<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AcceptExpenseRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Handle failed authorization for accepting an expense.
     *
     * @return void
     * @throws Illuminate\Http\Exceptions\HttpResponseException
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
}
