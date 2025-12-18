<?php

namespace App\View\Components\Dropdowns;

use App\Models\Role;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectRole extends Component
{
    public $options;
    /**
     * Create a new component instance.
     */
    public function __construct(public string $name,  public $selected = null)
    {

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->options = Role::pluck('name', 'id')->toArray();

        return view('components.dropdowns.select-role');
    }
}
