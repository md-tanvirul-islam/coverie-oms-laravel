<?php

namespace App\View\Components\Dropdowns;

use App\Models\User;
use App\Services\UserService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectUser extends Component
{
    public $options;
    private $userService;
    /**
     * Create a new component instance.
     */
    public function __construct(public string $name,  public $selected = null) {
        $this->userService = new UserService;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->options = $this->userService->dropdown();

        return view('components.dropdowns.select-user');
    }
}
