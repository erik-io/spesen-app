@props(['sortBy', 'label'])

@php
    $isCurrentSortColumn = $sortBy === $currentSortBy;
    $targetDirection = 'desc'; // Default sort direction for non-active columns

    // Default classes for arrows (faint, with hover effect)
    $upArrowClass = 'text-gray-300 group-hover:text-gray-500';
    $downArrowClass = 'text-gray-300 group-hover:text-gray-500';

    if ($isCurrentSortColumn) {
        // Set the opposite direction for the link
        $targetDirection = $currentSortDirection === 'asc' ? 'desc' : 'asc';

        // Highlight the currently active sort direction
        if ($currentSortDirection === 'asc') {
            $upArrowClass = 'text-gray-700'; // Active up arrow
        } else {
            $downArrowClass = 'text-gray-700'; // Active down arrow
        }
    }
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort_by' => $sortBy, 'sort_direction' => $targetDirection]) }}"
   class="group text-gray-500 hover:text-gray-700">
    <span class="relative inline-flex items-center">
        {{ $label }}
        <span class="fa-stack ml-1" style="font-size: 0.7em; margin-bottom: 0.2em; margin-right: -1.2em;">
            <i class="fas fa-sort-up fa-stack-1x {{ $upArrowClass }}"></i>
            <i class="fas fa-sort-down fa-stack-1x {{ $downArrowClass }}" style="margin-top: 0.25em;"></i>
        </span>
    </span>
</a>
