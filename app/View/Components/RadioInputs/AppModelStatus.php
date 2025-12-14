<?php

namespace App\View\Components\RadioInputs;

use App\Enums\AppModelStatus as EnumsAppModelStatus;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppModelStatus extends Component
{
    public $options = [];

    /**
     * Create a new component instance.
     */
    public function __construct(public string $name,  public $checked = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->options = EnumsAppModelStatus::options();
        // dd($this->options);
        return view('components.radio-inputs.app-model-status');
    }
}
