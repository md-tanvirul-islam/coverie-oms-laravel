<?php

namespace App\View\Components\Dropdowns;

use App\Services\EmployeeService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectEmployee extends Component
{
    public $options;
    private $employeeService;
    /**
     * Create a new component instance.
     */
    public function __construct(public string $name,  public $selected = null) {
        $this->employeeService = new EmployeeService;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->options = $this->employeeService->dropdown();

        return view('components.dropdowns.select-employee');
    }
}
