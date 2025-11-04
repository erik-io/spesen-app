<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expense Report') }} #{{ $expense->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Validation Errors (will be shown if rejection comment is missing) --}}
                    @if ($errors->any() && !$errors->has('rejection_comment'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <strong>{{ __('Error') }}</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Return to pending expenses --}}
                    <div class="mb-4 text-right">
                        <x-primary-button href="{{ url()->previous() }}">
                            {{ __('Back to List') }}
                        </x-primary-button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        {{-- Expense Details --}}
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Details</h3>
                            <dl class="mt-2 space-y-2">
                                {{-- Employee Name --}}
                                <div class="flex justify-between">
                                    <dt class="text-base font-medium text-gray-500">Name</dt>
                                    <dd class="text-base text-gray-900">
                                        {{ $expense->user->name }}
                                        (<a href="mailto:{{ $expense->user->email }}"
                                            class="text-indigo-600 hover:underline">{{ $expense->user->email }}</a>)
                                    </dd>
                                </div>
                                {{-- Expense Date --}}
                                <div class="flex justify-between">
                                    <dt class="text-base font-medium text-gray-500">Expense Date</dt>
                                    <dd class="text-base text-gray-900">{{ $expense->expense_date?->format('d.m.Y') }}</dd>
                                </div>
                                {{-- Submission Date --}}
                                <div class="flex justify-between">
                                    <dt class="text-base font-medium text-gray-500">Submission Date</dt>
                                    <dd class="text-base text-gray-900">{{ $expense->created_at?->format('d.m.Y H:i') }}</dd>
                                </div>
                                {{-- Cost Center --}}
                                <div class="flex justify-between">
                                    <dt class="text-base font-medium text-gray-500">Cost Center</dt>
                                    <dd class="text-base text-gray-900">{{ $expense->cost_center }}</dd>
                                </div>
                                {{-- Amount --}}
                                <div class="flex justify-between">
                                    <dt class="text-base font-medium text-gray-500">Amount</dt>
                                    <dd class="text-base font-bold text-gray-900">{{ number_format($expense->amount, 2, ',', '.') }}
                                        €
                                    </dd>
                                </div>
                                {{-- Status --}}
                                <div class="flex justify-between">
                                    <dt class="text-base font-medium text-gray-500">Status</dt>
                                    <dd class="text-base text-gray-900">
                                         <span class="px-2 inline-flex text-sm font-medium leading-5 rounded-full
                                                @if($expense->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($expense->status == 'approved') bg-green-100 text-green-800
                                                @elseif($expense->status == 'rejected') bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($expense->status) }}
                                         </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Action Area --}}
                        <div x-data="{
                            showRejectForm: {{ $errors->has('rejection_comment') ? 'true' : 'false' }},
                            maxChars: 5000,
                            comment: '{{ old('rejection_comment') }}'
                            }">
                            @if ($expense->status == 'pending')
                                {{-- Button Container (Approve / Reject) --}}
                                <div x-show="!showRejectForm" x-transition:enter class="space-y-2">
                                    <h3 class="text-lg font-medium text-gray-900">{{ __('Actions') }}</h3>
                                    <div class="mt-2 grid grid-cols-2 gap-4">
                                        {{-- Approve Button --}}
                                        <x-primary-button type="button"
                                                          x-data=""
                                                          x-on:click.prevent="$dispatch('open-modal', 'confirm-approval')"
                                                          class="w-full justify-center bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:ring-green-500">
                                            {{ __('Approve') }}
                                        </x-primary-button>
                                        {{-- Reject Button --}}
                                        <x-danger-button type="button" @click="showRejectForm = true"
                                                         class="w-full justify-center">
                                            {{ __('Reject') }}
                                        </x-danger-button>
                                    </div>
                                </div>
                                {{-- Reject Form --}}
                                <form method="POST"
                                      action="{{ route('expenses.management.reject', $expense) }}"
                                      x-show="showRejectForm" x-cloak x-transition
                                      x-ref="rejectForm"
                                      x-on:submit.prevent="$dispatch('open-modal', 'confirm-rejection')">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <x-input-label for="rejection_comment"
                                                       :value="__('Reason for Rejection (Required)')"/>
                                        <textarea id="rejection_comment"
                                                  name="rejection_comment"
                                                  rows="4"
                                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                  x-model="comment"
                                                  maxlength="5000"
                                                  required>{{ old('rejection_comment') }}</textarea>
                                        <x-input-error :messages="$errors->get('rejection_comment')" class="mt-2"/>

                                        {{-- Character Counter Display --}}
                                        <div class="mt-1 text-base text-gray-500 text-right">
                                            <span x-text="comment.length"></span> / <span x-text="maxChars"></span>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex space-x-4">
                                        {{-- Reject Button (triggers modal) --}}
                                        <x-danger-button type="submit" class="w-full justify-center">
                                            {{ __('Confirm Rejection') }}
                                        </x-danger-button>
                                        {{-- Cancel Button (hides form) --}}
                                        <x-secondary-button type="button" @click="showRejectForm = false"
                                                            class="w-full justify-center">
                                            {{ __('Cancel') }}
                                        </x-secondary-button>
                                    </div>

                                    {{-- Reject Confirmation Modal --}}
                                    <x-modal :name="'confirm-rejection'">
                                        <div class="p-6">
                                            <h2 class="text-lg font-medium text-gray-900">
                                                {{ __('Confirm Rejection') }}
                                            </h2>
                                            <p class="mt-2 text-base text-gray-600">
                                                {{ __('Are you sure you want to reject this expense report? The employee will see the comment you provided.') }}
                                            </p>
                                            <div class="mt-6 flex justify-end">
                                                {{-- Cancel Button --}}
                                                <x-secondary-button
                                                    x-on:click="$dispatch('close-modal', 'confirm-rejection')">
                                                    {{ __('Cancel') }}
                                                </x-secondary-button>
                                                {{-- Submit Button --}}
                                                <x-danger-button type="button"
                                                                 x-on:click="$refs.rejectForm.submit()"
                                                                 class="ms-3">
                                                    {{ __('Yes, Reject') }}
                                                </x-danger-button>
                                            </div>
                                        </div>
                                    </x-modal>
                                </form>

                                {{-- Approve Confirmation Modal --}}
                                <x-modal :name="'confirm-approval'">
                                    <form method="POST" action="{{ route('expenses.management.approve', $expense) }}"
                                          class="p-6">
                                        @csrf
                                        @method('PATCH')
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Confirm Approval') }}
                                        </h2>
                                        <p class="mt-2 text-base text-gray-600">
                                            {{ __('Are you sure you want to approve this expense report?') }}
                                        </p>
                                        <div class="mt-6 flex justify-end">
                                            {{-- Cancel Button --}}
                                            <x-secondary-button
                                                x-on:click="$dispatch('close-modal', 'confirm-approval')">
                                                {{ __('Cancel') }}
                                            </x-secondary-button>
                                            {{-- Submit Button --}}
                                            <x-primary-button
                                                class="ms-3 bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:ring-green-500">
                                                {{ __('Yes, Approve') }}
                                            </x-primary-button>
                                        </div>
                                    </form>
                                </x-modal>
                                {{-- Show rejection comment if it's rejected --}}
                            @elseif ($expense->status == 'rejected' && $expense->rejection_comment)
                                <h3 class="text-lg font-medium text-red-900">{{ __('Reason for Rejection') }}</h3>
                                <div class="mt-2 p-4 bg-gray-50 border-l-4 border-red-400">
                                    <p class="text-base text-gray-700 break-words hyphens-auto">
                                        {!! nl2br(e($expense->rejection_comment)) !!}
                                    </p>
                                </div>
                                {{-- Show a note if it's already approved --}}
                            @elseif ($expense->status == 'approved')
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Status') }}</h3>
                                <div class="mt-2 p-4 bg-gray-50 border-l-4 border-green-400">
                                    <p class="text-base text-gray-700">
                                        {{ __('This expense report has already been approved.') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
