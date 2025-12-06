<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\DashboardService;

class StatisticCards extends Component
{
    public ?string $from = null;
    public ?string $to = null;
    
    public int $confirmedQty = 0;
    public int $returnQty = 0;
    public int $deliveryQty = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function updated($property)
    {
        $this->loadData();
    }

    private function loadData()
    {
        $service = app(DashboardService::class);
        // CARD VALUES
        $this->confirmedQty = $service->confirmedOrderQuantity($this->from, $this->to);
        $this->returnQty    = $service->paidInvoiceReturnQuantity($this->from, $this->to);
        $this->deliveryQty  = $service->paidInvoiceDeliveryQuantity($this->from, $this->to);
    }

    public function render()
    {
        return view('livewire.statistic-cards');
    }
}
