@use('App\Models\Expense')
<x-app-layout>
    <x-slot name="title">
        {{ __('Submit New Expense Report') }}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Submit New Expense Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Initialize Alpine data context for the form inputs --}}
                <div class="p-6 text-gray-900 dark:text-gray-100"
                     x-data="{
                         amountInput: '{{ old('amount', '') }}',
                         dateInput: '{{ old('expense_date', '') }}',
                         costCenterInput: '{{ old('cost_center', '') }}'
                     }">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div
                            class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 rounded">
                            <strong>{{ __('Error') }}</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Expense Form --}}
                    <form method="POST"
                          action="{{ route('expenses.store') }}"
                          x-ref="createExpenseForm"
                          x-on:submit.prevent="$dispatch('open-modal', 'confirm-submission')">
                        @csrf

                        {{-- Expense Amount --}}
                        <div class="mb-4">
                            <x-input-label for="amount">
                                {{ __('Amount (in EUR)') }}
                                <span class="text-red-500">*</span>
                            </x-input-label>
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01"
                                          name="amount"
                                          x-model="amountInput"
                                          required autofocus placeholder="0,00" min="0.01"
                                          max="99999999.99"/>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2"/>
                        </div>

                        {{-- Expense Date --}}
                        <div class="mb-4">
                            <x-input-label for="expense_date">
                                {{ __('Date') }}
                                <span class="text-red-500">*</span>
                            </x-input-label>
                            <x-text-input id="expense_date" class="block mt-1 w-full" type="date" name="expense_date"
                                          x-model="dateInput"
                                          required max="{{ now()->toDateString() }}"
                                          min="{{ now()->subDays(Expense::MAX_SUBMISSION_AGE_DAYS)->toDateString() }}"/>
                            <x-input-error :messages="$errors->get('expense_date')" class="mt-2"/>
                        </div>

                        {{-- Cost Center --}}
                        <div class="mb-4">
                            <x-input-label for="cost_center">
                                {{ __('Cost Center') }}
                                <span class="text-red-500">*</span>
                            </x-input-label>
                            <x-text-input id="cost_center" class="block mt-1 w-full" type="text" name="cost_center"
                                          x-model="costCenterInput"
                                          required maxlength="50"/>
                            <x-input-error :messages="$errors->get('cost_center')" class="mt-2"/>
                        </div>

                        {{-- Required fields info text --}}
                        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                            <span class="text-red-500 dark:text-red-400">*</span> {{ __('Indicates required fields') }}
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button type="submit">
                                {{ __('Submit') }}
                            </x-primary-button>
                        </div>
                    </form>

                    {{-- Confirmation Modal --}}
                    <x-modal name="confirm-submission">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('modals.submission.title') }}
                            </h2>
                            <p class="mt-2 text-base text-gray-600 dark:text-gray-300">
                                {{ __('modals.submission.body') }}
                            </p>

                            {{-- Dynamic Data Display --}}
                            <div
                                class="mt-4 space-y-2 text-sm text-gray-800 dark:text-gray-200 border-t border-b border-gray-200 dark:border-gray-600 py-4">
                                <div class="flex justify-between">
                                    <span
                                        class="font-medium text-gray-600 dark:text-gray-400">{{ __('Amount') }}:</span>
                                    {{-- Display formatted amount, handling commas and ensuring 2 decimal places --}}
                                    <span class="font-bold text-gray-900 dark:text-gray-100"
                                          x-text="`${parseFloat(String(amountInput).replace(',', '.')).toFixed(2)} {{ __('EUR') }}`"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Date') }}:</span>
                                    <span class="font-bold text-gray-900 dark:text-gray-100"
                                          x-text="dateInput || '{{ __('Not specified') }}'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span
                                        class="font-medium text-gray-600 dark:text-gray-400">{{ __('Cost Center') }}:</span>
                                    <span class="font-bold text-gray-900 dark:text-gray-100"
                                          x-text="costCenterInput || '{{ __('Not specified') }}'"></span>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close-modal', 'confirm-submission')">
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                                <x-primary-button type="button"
                                                  class="ms-3"
                                                  x-on:click="$refs.createExpenseForm.submit()">
                                    {{ __('Submit') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </x-modal>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
