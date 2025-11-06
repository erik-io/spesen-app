@props(['sortBy', 'label'])
@php
    $currentSortBy = request()->query('sort_by', 'created_at');
    $currentSortDirection = request()->query('sort_direction', 'desc');
    $targetDirection = 'asc';
    $icon = '';

    if ($currentSortBy === $sortBy) {
    $targetDirection = $currentSortDirection === 'asc' ? 'desc' : 'asc';
    $icon = $currentSortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
    }
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort_by' => $sortBy, 'sort_direction' => $targetDirection]) }}"
   class="inline-flex items-center group">

    <span>{{ $label }}</span>

    {{-- Icon container --}}
    <span class="ms-1.5">
        @if ($isSorted)
            {{-- Column is currently sorted. Show the active direction icon. --}}
            @if ($currentSortDirection === 'asc')
                <i class="fas fa-sort-up" aria-hidden="true"></i>
            @else
                <i class="fas fa-sort-down" aria-hidden="true"></i>
            @endif
        @else
            {{-- Column is not sorted. Show a neutral sort icon on hover. --}}
            <i class="fas fa-sort text-gray-300 group-hover:text-gray-500" aria-hidden="true"></i>
        @endif
    </span>
</a>
