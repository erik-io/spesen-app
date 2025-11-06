<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SortableLink extends Component
{
    public string $sortBy;
    public string $label;
    public string $currentSortBy;
    public string $currentSortDirection;

    /**
     * Create a new component instance.
     */
    public function __construct(string $sortBy, string $label)
    {
        $this->sortBy = $sortBy;
        $this->label = $label;
        $this->currentSortBy = request('sort_by', 'created_at');
        $this->currentSortDirection = request('sort_direction', 'desc');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sortable-link');
    }
}
