<?php

namespace App\View\Components\Dropdowns;

use App\Services\IncomeTypeService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectIncomeType extends Component
{
    public $options;
    private $incomeTypeService;
    /**
     * Create a new component instance.
     */
    public function __construct(public string $name,  public $selected = null)
    {
        $this->incomeTypeService = new IncomeTypeService;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->options = $this->incomeTypeService->dropdown();

        return view('components.dropdowns.select-income-type');
    }
}
