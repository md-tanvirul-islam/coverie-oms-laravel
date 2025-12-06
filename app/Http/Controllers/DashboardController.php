<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function welcome()
    {
        return view("welcome");
    }

    public function dashboard(Request $request)
    {
        $mode = $request->get('mode', 'days'); // 'days' or 'months'
        $from = $request->get('from');
        $to   = $request->get('to');
        $year = $request->get('year');

        if ($mode === 'months') {
            $chartData = $this->service->collectableAmountByMonths($year ? (int) $year : null);
        } else {
            // days mode
            $chartData = $this->service->collectableAmountByDays(12, $from, $to);
        }

        // cards (we pass from/to)
        $confirmedQty = $this->service->confirmedOrderQuantity($from, $to);
        $returnQty = $this->service->paidInvoiceReturnQuantity($from, $to);
        $deliveryQty = $this->service->paidInvoiceDeliveryQuantity($from, $to);

        // prepare labels & values for chart.js
        $labels = $chartData->pluck('label')->toArray();
        $values = $chartData->pluck('value')->toArray();

        return view('dashboard', compact(
            'mode',
            'labels',
            'values',
            'confirmedQty',
            'returnQty',
            'deliveryQty',
            'from',
            'to',
            'year'
        ));
    }
}
