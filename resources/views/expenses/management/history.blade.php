@use('App\Models\Expense')
@php
    $statusOptions = [
        'all' => __('All'),
        Expense::STATUS_PENDING => __('Pending'),
        Expense::STATUS_APPROVED => __('Approved'),
        Expense::STATUS_REJECTED => __('Rejected'),
    ];
@endphp
<x-app-layout>
    <x-slot name="title">
        {{ __('Expense History') }}
    </x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Expense History') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('View and track all expense submissions and their approval status') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div
                class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-100 dark:border-none">
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        @php($currentStatus = request('status', 'all'))
                        <div class="flex items-center space-x-3">
                            <div
                                class="flex items-center justify-center w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                                    {{ __('Total Expenses') }}
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">
                                    {{ Expense::count() }} {{ __('total expenses') }},
                                    @switch($currentStatus)
                                        @case('all')
                                            {{ $pendingCount }} {{ __('awaiting review') }}
                                            @break
                                        @case(Expense::STATUS_PENDING)
                                            {{ $expenses->total() }} {{ __('awaiting review') }}
                                            @break
                                        @case(Expense::STATUS_APPROVED)
                                            {{ $expenses->total() }} {{ __('approved') }}
                                            @break
                                        @case(Expense::STATUS_REJECTED)
                                            {{ $expenses->total() }} {{ __('rejected') }}
                                            @break
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('expenses.management.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-semibold rounded-lg shadow-md transition duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ __('View Pending') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Success flash message --}}
                    @if (session('success'))
                        <div
                            class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 border border-green-300 dark:border-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div
                        class="flex flex-col sm:flex-row justify-between items-center mb-6 flex-wrap space-y-4 sm:space-y-0 sm:space-x-4">
                        {{-- flex justify-end items-center mb-6 flex-wrap gap-4 --}}
                        {{-- Filter form (left) --}}
                        <form method="GET" action="{{ route('expenses.management.history') }}"
                              class="flex items-center space-x-2">
                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                            <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">
                            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Filter') }}
                            </label>

                            <select name="status" id="status"
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 focus:ring-opacity-50 rounded-md shadow-sm"
                                    onchange="this.form.submit()">
                                @foreach($statusOptions as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ request('status', 'all') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        {{-- Per-page form (right) --}}
                        <form method="GET" action="{{ route('expenses.management.history') }}"
                              class="flex items-center space-x-2">
                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                            <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">
                            <input type="hidden" name="status" value="{{ request('status', 'all') }}">

                            <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Showing') }}
                            </label>
                            <select name="per_page" id="per_page"
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 focus:ring-opacity-50 rounded-md shadow-sm"
                                    onchange="this.form.submit()">
                                @foreach ([10, 25, 50, 100] as $value)
                                    <option
                                        value="{{ $value }}" {{ request('per_page', 10) == $value ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('per page') }}
                            </label>
                        </form>
                    </div>

                    {{-- Expense Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                {{-- ID --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('ID') }}
                                </th>
                                {{-- Employee --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <x-sortable-link sortBy="user_id" label="{{ __('Employee Name') }}"/>
                                </th>
                                {{-- Submission Date --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <x-sortable-link sortBy="created_at" label="{{ __('Submission Date') }}"/>
                                </th>
                                {{-- Expense Date --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <x-sortable-link sortBy="expense_date" label="{{ __('Expense Date') }}"/>
                                </th>
                                {{-- Cost Center --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <x-sortable-link sortBy="cost_center" label="{{ __('Cost Center') }}"/>
                                </th>
                                {{-- Amount --}}
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <x-sortable-link sortBy="amount" label="{{ __('Amount') }}"/>
                                </th>
                                {{-- Status (pending, approved, rejected) --}}
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <x-sortable-link sortBy="status" label="{{ __('Status') }}"/>
                                </th>
                                {{-- Details --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Details') }}
                                </th>
                            </tr>
                            </thead>

                            @forelse ($expenses as $expense)
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                    {{-- ID --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $expense->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100 w-48">
                                        @php($employeeName = $expense->user?->name ?? __('messages.general.unknown_user'))
                                        <span class="block max-w-[9rem] truncate"
                                              title="{{ $employeeName }}">{{ $employeeName }}</span>
                                    </td>
                                    {{-- Submission Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $expense->created_at?->isoFormat('L') }}
                                    </td>
                                    {{-- Expense Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $expense->expense_date?->isoFormat('L') }}
                                    </td>
                                    {{-- Cost Center --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100 w-40">
                                        @php($costCenter = $expense->cost_center ?? __('messages.general.unknown_cost_center'))
                                        <span class="block max-w-[9rem] truncate"
                                              title="{{ $costCenter }}">{{ $costCenter }}</span>
                                    </td>
                                    {{-- Amount --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                        <x-money :amount="$expense->amount"/>
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <x-expense-status :status="$expense->status"/>
                                    </td>
                                    {{-- Action Button (Redirect to Expense Details) --}}
                                    <td class="py-4 text-center text-sm font-medium whitespace-nowrap">
                                        <a href="{{ route('expenses.management.show', $expense) }}"
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition duration-150"
                                           title="{{ __('View Details') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="size-5 mx-auto">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            @empty
                                <tbody class="bg-white dark:bg-gray-800">
                                <tr>
                                    <td colspan="8"
                                        class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">
                                        {{ __('expenses.empty.all') }}
                                    </td>
                                </tr>
                                </tbody>
                            @endforelse
                        </table>
                    </div>
                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $expenses->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
