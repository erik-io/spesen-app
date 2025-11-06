@props(['sortBy', 'label'])

@php
    $isCurrentSortColumn = $sortBy === $currentSortBy;

    if ($isCurrentSortColumn) {
        $targetDirection = $currentSortDirection === 'asc' ? 'desc' : 'asc';
        $icon = $currentSortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
    } else {
        $targetDirection = 'desc';
        $icon = 'fas fa-sort text-gray-300 group-hover:text-gray-500';
    }
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort_by' => $sortBy, 'sort_direction' => $targetDirection]) }}"
   class="inline-flex items-center group text-gray-500 hover:text-gray-700">
    {{ $label }}
    <i class="ml-1 {{ $icon }}"></i>
</a>
