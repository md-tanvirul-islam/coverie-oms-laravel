<?php

namespace App\Http\Controllers;

use App\DataTables\ModeratorCommissionReportDataTable;
use App\Enums\PaidInvoiceType;
use App\Models\CourierPaidInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // public function moderatorCommissionReport(ModeratorCommissionReportDataTable $dataTable)
    // {
    //     return $dataTable->render('reports.moderator_commission_datatable');
    // }

    public function moderatorCommissionDailyReport(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');

        $query = CourierPaidInvoice::query()
            ->select([
                'moderators.id as moderator_id',
                'moderators.name',
                'moderators.code',
                'orders.order_date',
                DB::raw('SUM(orders.quantity) as total_quantity'),
                DB::raw('SUM(orders.quantity * moderators.commission_fee_per_order) as total_commission')
            ])
            ->join('orders', 'orders.id', '=', 'courier_paid_invoices.order_id')
            ->join('moderators', 'moderators.id', '=', 'orders.moderator_id')
            ->where('courier_paid_invoices.invoice_type', PaidInvoiceType::TYPE_DELIVERY)
            ->groupBy('orders.order_date', 'moderators.id');

        if ($from) {
            $query->whereDate('orders.order_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('orders.order_date', '<=', $to);
        }

        $reports = $query->paginate(50);

        return view('reports.moderator_commission_daily', compact('reports'));
    }

    public function moderatorCommissionMonthlyReport(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');

        $query = CourierPaidInvoice::query()
            ->select([
                'moderators.id as moderator_id',
                'moderators.name',
                'moderators.code',
                DB::raw("YEAR(orders.order_date) as order_year"),
                DB::raw("MONTH(orders.order_date) as order_month"),
                DB::raw('SUM(orders.quantity) as total_quantity'),
                DB::raw('SUM(orders.quantity * moderators.commission_fee_per_order) as total_commission')
            ])
            ->join('orders', 'orders.id', '=', 'courier_paid_invoices.order_id')
            ->join('moderators', 'moderators.id', '=', 'orders.moderator_id')
            ->where('courier_paid_invoices.invoice_type', PaidInvoiceType::TYPE_DELIVERY)
            ->groupBy('moderators.id', DB::raw("YEAR(orders.order_date)"), DB::raw("MONTH(orders.order_date)"))
            ->orderBy('order_year')
            ->orderBy('order_month');

        if ($from) {
            $query->whereDate('orders.order_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('orders.order_date', '<=', $to);
        }

        $reports = $query->paginate(50);

        return view('reports.moderator_commission_monthly', compact('reports'));
    }
}
