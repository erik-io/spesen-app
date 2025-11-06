<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Expense Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Success flash message --}}
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-end items-center mb-4 space-x-4">
                        {{-- Per Page Form --}}
                        <form method="GET" action="{{ route('expenses.index') }}" class="flex items-center space-x-2">
                            {{-- Hidden inputs to preserve sorting --}}
                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                            <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">

                            <label for="per_page" class="block text-sm font-medium text-gray-700">
                                {{ __('Show') }}
                            </label>
                            <select name="per_page" id="per_page"
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    onchange="this.form.submit()">
                                @foreach ([10, 25, 50, 100] as $value)
                                    <option
                                        value="{{ $value }}" {{ request('per_page', 10) == $value ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="per_page" class="block text-sm font-medium text-gray-700">
                                {{ __('per page') }}
                            </label>
                        </form>

                        {{-- Button to create new expense --}}
                        <x-primary-button href="{{ route('expenses.create') }}">
                            {{ __('Submit New Expense') }}
                        </x-primary-button>
                    </div>

                    {{-- Expense Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                {{-- ID --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                {{-- Submission Date --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <x-sortable-link sortBy="created_at" label="{{ __('Submission Date') }}"/>
                                </th>
                                {{-- Expense Date --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <x-sortable-link sortBy="expense_date" label="{{ __('Expense Date') }}"/>
                                </th>
                                {{-- Cost Center --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cost Center
                                </th>
                                {{-- Amount --}}
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <x-sortable-link sortBy="amount" label="{{ __('Amount') }}"/>
                                </th>
                                {{-- Status (pending, approved, rejected) --}}
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <x-sortable-link sortBy="status" label="{{ __('Status') }}"/>
                                </th>
                                <th scope="col" class="px-3 py-3 text-right">
                                    <span class="sr-only">Reason</span>
                                </th>
                            </tr>
                            </thead>

                            @forelse ($expenses as $expense)
                                <tbody x-data="{ open: false }" class="bg-white">
                                <tr>
                                    {{-- ID --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $expense->id }}
                                    </td>
                                    {{-- Submission Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $expense->created_at?->format('d.m.Y') }}
                                    </td>
                                    {{-- Expense Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $expense->expense_date?->format('d.m.Y') }}
                                    </td>
                                    {{-- Cost Center --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $expense->cost_center }}
                                    </td>
                                    {{-- Amount --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        {{ number_format($expense->amount, 2, ',', '.') }} €
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2 inline-flex text-sm font-medium leading-5 rounded-full
                                                {{-- 'approved' (green), 'pending' (yellow), 'rejected' (red) --}}
                                                @if($expense->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($expense->status == 'approved') bg-green-100 text-green-800
                                                @elseif($expense->status == 'rejected') bg-red-100 text-red-800
                                                @endif">
                                                {{-- Capitalize the first character --}}
                                                {{ ucfirst($expense->status) }}
                                            </span>
                                    </td>
                                    {{-- Action Cell (Show/Hide Rejection Reason) --}}
                                    <td class="px-3 py-4 text-center text-sm font-medium">
                                        @if($expense->status == 'rejected' && $expense->rejection_comment !== null && $expense->rejection_comment !== '')
                                            <button
                                                @click='open = !open'
                                                class='inline-flex items-center text-indigo-600 hover:text-indigo-900 text-sm font-medium'>
                                                <span x-show='!open' x-cloak>{{ __('View Reason') }}</span>
                                                <span x-show='open' x-cloak>{{ __('Hide Reason') }}</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                {{-- Rejection Comment (hidden by default) --}}
                                @if($expense->status == 'rejected' && $expense->rejection_comment !== null)
                                    <tr x-show="open" x-transition x-cloak>
                                        <td colspan="7" class="p-0">
                                            <div class="p-4 bg-gray-50 border-l-4 border-red-400">
                                                <h4 class="font-bold text-sm text-red-800">{{ __('Rejection Comment') }}</h4>
                                                <p class="mt-1 text-sm text-gray-700 break-all hyphens-auto">
                                                    {{-- Convert newlines to <br> tags and explicit handling of HTML attributes --}}
                                                    {!! nl2br(e($expense->rejection_comment)) !!}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            @empty
                                <tbody class="bg-white">
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
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
