<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit new expense report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <strong>Validation Error!</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Expense Form --}}
                    <form method="POST" action="{{ route('expenses.store') }}">
                        @csrf
                        {{-- Expense Amount --}}
                        <div class="mb-4">
                            <x-input-label for="amount" :value="__('Amount (in EUR)')"/>
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" name="amount"
                                          :value="old('amount')" required autofocus placeholder="0,00" min="0.01"
                                          max="99999999.99"/>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2"/>
                        </div>

                        {{-- Expense Date --}}
                        <div class="mb-4">
                            <x-input-label for="expense_date" :value="__('Date')"/>
                            <x-text-input id="expense_date" class="block mt-1 w-full" type="date" name="expense_date"
                                          :value="old('expense_date')" required max="{{ now()->toDateString() }}"/>
                            <x-input-error :messages="$errors->get('expense_date')" class="mt-2"/>
                        </div>

                        {{-- Cost Center --}}
                        <div class="mb-4">
                            <x-input-label for="cost_center" :value="__('Cost Center')"/>
                            <x-text-input id="cost_center" class="block mt-1 w-full" type="text" name="cost_center"
                                          :value="old('cost_center')" required maxlength="50"/>
                            <x-input-error :messages="$errors->get('cost_center')" class="mt-2"/>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Submit') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
