@use('App\Models\Expense')
<x-app-layout>
    <x-slot name="title">
        {{ __('Expense History') }}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Archive') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Success flash message --}}
                    @if (session('success'))
                        <div
                            class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 border border-green-300 dark:border-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-end items-center mb-4 space-x-4">
                        {{-- GET-Form --}}
                        <form method="GET" action="{{ route('expenses.management.history') }}"
                              class="flex items-center space-x-2">
                            {{-- Hidden inputs to preserve sorting --}}
                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                            <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">

                            {{-- Filter --}}
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Filter') }}
                            </label>
                            <select name="status" id="status"
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 focus:ring-opacity-50 rounded-md shadow-sm"
                                    onchange="this.form.submit()">
                                @php
                                    $statuses = ['all' => __('All'), Expense::STATUS_PENDING => __('Pending'), Expense::STATUS_APPROVED => __('Approved'), Expense::STATUS_REJECTED => __('Rejected')];
                                @endphp
                                @foreach($statuses as $key => $label)
                                    <option
                                        value="{{ $key }}" {{ request('status', 'all') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Per Page --}}
                            <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Show') }}
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

                        {{-- Button to show pending expenses --}}
                        <x-primary-button href="{{ route('expenses.management.index') }}">
                            {{ __('View Pending') }}
                        </x-primary-button>
                    </div>

                    {{-- Expense Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                {{-- ID --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    ID
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
                                    Details
                                </th>
                            </tr>
                            </thead>

                            @forelse ($expenses as $expense)
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    {{-- ID --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $expense->id }}
                                    </td>
                                    {{-- Employee Name --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $expense->user->name }}
                                    </td>
                                    {{-- Submission Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $expense->created_at?->format('d.m.Y') }}
                                    </td>
                                    {{-- Expense Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $expense->expense_date?->format('d.m.Y') }}
                                    </td>
                                    {{-- Cost Center --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $expense->cost_center }}
                                    </td>
                                    {{-- Amount --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-900 dark:text-gray-100">
                                        {{ number_format($expense->amount, 2, ',', '.') }} €
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2 inline-flex text-sm font-medium leading-5 rounded-full
                                                @if($expense->status == Expense::STATUS_PENDING) bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                                @elseif($expense->status == Expense::STATUS_APPROVED) bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                                @elseif($expense->status == Expense::STATUS_REJECTED) bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                                @endif">
                                                {{ ucfirst($expense->status) }}
                                            </span>
                                    </td>
                                    {{-- Action Button (Redirect to Expense Details) --}}
                                    <td class="px-3 py-4 text-center text-sm font-medium">
                                        <a href="{{ route('expenses.management.show', $expense) }}"
                                           class='inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm font-medium'>
                                            {{ __('View Details') }}
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            @empty
                                <tbody class="bg-white dark:bg-gray-800">
                                <tr>
                                    <td colspan="8"
                                        class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">
                                        {{ __('No expense reports found.') }}
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
