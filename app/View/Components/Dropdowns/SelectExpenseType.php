<?php

namespace App\View\Components\Dropdowns;

use App\Services\ExpenseTypeService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectExpenseType extends Component
{
    public $options;
    private $expenseTypeService;
    /**
     * Create a new component instance.
     */
    public function __construct(public string $name,  public $selected = null) {
        $this->expenseTypeService = new ExpenseTypeService;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->options = $this->expenseTypeService->dropdown();

        return view('components.dropdowns.select-expense-type');
    }
}
