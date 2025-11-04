<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expense Management') }}
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

                    {{-- Button to show pending expenses --}}
                    <div class="mb-4 text-right">
                        <x-primary-button href="{{ route('expenses.management.index') }}">
                            {{ __('View Pending') }}
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
                                {{-- Employee --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Employee
                                </th>
                                {{-- Expense Date --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Expense Date
                                </th>
                                {{-- Cost Center --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cost Center
                                </th>
                                {{-- Amount --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                {{-- Status (pending, approved, rejected) --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                {{-- Details --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Details
                                </th>
                            </tr>
                            </thead>

                            @forelse ($expenses as $expense)
                                <tbody class="bg-white">
                                <tr>
                                    {{-- ID --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $expense->id }}
                                    </td>
                                    {{-- Employee Name --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $expense->user->name }}
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ number_format($expense->amount, 2, ',', '.') }} €
                                    </td>
                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    {{-- Action Button (Redirect to Expense Details) --}}
                                    <td class="px-3 py-4 text-center text-sm font-medium">
                                        <a href="{{ route('expenses.management.show', $expense) }}"
                                           class='inline-flex items-center text-indigo-600 hover:text-indigo-900 text-sm font-medium'>
                                            {{ __('View Details') }}
                                        </a>
                                    </td>
                                </tr>
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
