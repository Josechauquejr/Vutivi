<?php

namespace App\View\Components\resources;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class resourceDetail extends Component
{
    public $resource;

    /**
     * Create a new component instance.
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.resources.resource-detail');
    }
}
