<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeCommissionReportDataTable;
use App\Enums\PaidInvoiceType;
use App\Models\CourierPaidInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // public function employeeCommissionReport(EmployeeCommissionReportDataTable $dataTable)
    // {
    //     return $dataTable->render('reports.employee_commission_datatable');
    // }

    public function employeeCommissionDailyReport(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');

        $query = CourierPaidInvoice::query()
            ->select([
                'employees.id as employee_id',
                'employees.name',
                'employees.code',
                'orders.order_date',
                DB::raw('SUM(orders.quantity) as total_quantity'),
                DB::raw('SUM(orders.quantity * employees.commission_fee_per_order) as total_commission')
            ])
            ->join('orders', 'orders.id', '=', 'courier_paid_invoices.order_id')
            ->join('employees', 'employees.id', '=', 'orders.employee_id')
            ->where('courier_paid_invoices.invoice_type', PaidInvoiceType::TYPE_DELIVERY)
            ->groupBy('orders.order_date', 'employees.id');

        if ($from) {
            $query->whereDate('orders.order_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('orders.order_date', '<=', $to);
        }

        $reports = $query->paginate(50);

        return view('reports.employee_commission_daily', compact('reports'));
    }

    public function employeeCommissionMonthlyReport(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');

        $query = CourierPaidInvoice::query()
            ->select([
                'employees.id as employee_id',
                'employees.name',
                'employees.code',
                DB::raw("YEAR(orders.order_date) as order_year"),
                DB::raw("MONTH(orders.order_date) as order_month"),
                DB::raw('SUM(orders.quantity) as total_quantity'),
                DB::raw('SUM(orders.quantity * employees.commission_fee_per_order) as total_commission')
            ])
            ->join('orders', 'orders.id', '=', 'courier_paid_invoices.order_id')
            ->join('employees', 'employees.id', '=', 'orders.employee_id')
            ->where('courier_paid_invoices.invoice_type', PaidInvoiceType::TYPE_DELIVERY)
            ->groupBy('employees.id', DB::raw("YEAR(orders.order_date)"), DB::raw("MONTH(orders.order_date)"))
            ->orderBy('order_year')
            ->orderBy('order_month');

        if ($from) {
            $query->whereDate('orders.order_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('orders.order_date', '<=', $to);
        }

        $reports = $query->paginate(50);

        return view('reports.employee_commission_monthly', compact('reports'));
    }
}
