<?php

namespace App\Livewire;

use App\Services\DashboardService;
use Carbon\Carbon;
use Livewire\Component;

class CollectedAmountBarChart extends Component
{
    public string $mode = 'days';   // days | months
    public ?string $from = null;
    public ?string $to = null;
    public ?int $year = null;

    public array $labels = [];
    public array $values = [];

    public function mount()
    {
        $this->year = now()->year;
        $this->loadData();
        $this->dispatch('refreshChart', labels: $this->labels, values: $this->values);
    }
    public function updated($property)
    {
        $this->loadData();
        $this->dispatch('refreshChart', labels: $this->labels, values: $this->values);
    }

    private function loadData()
    {
        $service = app(DashboardService::class);

        $fix_interval = 30;

        // CHART DATA
        if ($this->mode === 'months') {
            $chart = $service->collectableAmountByMonths($this->year);
        } else {
            if ($this->from && !$this->to) {
                $this->to = Carbon::parse($this->from)->addDays($fix_interval - 1)->toDateString();
            }
            if (!$this->from && $this->to) {
                $this->from = Carbon::parse($this->to)->subDays($fix_interval - 1)->toDateString();
            }
            $chart = $service->collectableAmountByDays(30, $this->from, $this->to);
        }

        $this->labels = $chart->pluck('label')->toArray();
        $this->values = $chart->pluck('value')->toArray();
    }

    public function render()
    {
        return view('livewire.collected-amount-bar-chart');
    }
}
