<?php

namespace App\View\Components\Dropdowns;

use App\Services\ItemService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectItem extends Component
{
    public $options;
    private $itemService;
    /**
     * Create a new component instance.
     */
    public function __construct(public string $name,  public $selected = null) {
        $this->itemService = new ItemService;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->options = $this->itemService->dropdown();

        return view('components.dropdowns.select-item');
    }
}
