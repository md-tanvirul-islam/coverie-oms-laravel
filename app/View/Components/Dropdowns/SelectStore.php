<?php

namespace App\View\Components\Dropdowns;

use App\Enums\StoreType;
use App\Services\StoreService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectStore extends Component
{
    public $options;
    private $storeService;

    /**
     * Create a new component instance.
     */
    public function __construct(public string $name,  public $selected = null) {
        $this->storeService = new StoreService;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->options = $this->storeService->dropdown();

        return view('components.dropdowns.select-store');
    }
}
