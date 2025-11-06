@props(['sortBy', 'label'])
@php
    $currentSortBy = request()->query('sort_by', 'created_at');
    $currentSortDirection = request()->query('sort_direction', 'desc');
    $targetDirection = 'asc';
    $icon = '';

    if ($currentSortBy === $sortBy) {
    $targetDirection = $currentSortDirection === 'asc' ? 'desc' : 'asc';
    $icon = $currentSortDirection === 'asc' ? ' &#9650;' : ' &#9660;';
    }
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort_by' => $sortBy, 'sort_direction' => $targetDirection]) }}"
   class="inline-flex items-center group text-gray-500 hover:text-gray-700">
    {{ $label }}
    <span class="ms-1 text-gray-500">
        {{-- This will render the HTML arrow entities --}}
        {!! $icon !!}
    </span>
</a>
