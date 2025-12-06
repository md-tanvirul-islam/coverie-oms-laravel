<?php

namespace App\Services;

use App\Enums\PaidInvoiceType;
use App\Models\Order;
use App\Models\CourierPaidInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get collectable amount for last N days (or between from/to if provided).
     * Returns collection of ['label' => '2025-12-01', 'value' => 123.45]
     */
    public function collectableAmountByDays(int $days = 12, ?string $from = null, ?string $to = null): Collection
    {
        if ($from && $to) {
            $start = Carbon::parse($from)->startOfDay();
            $end = Carbon::parse($to)->endOfDay();
            $periodDays = $start->diffInDays($end) + 1;
            $days = min(max(1, $periodDays), 365); // limit to sane range
        } else {
            $end = Carbon::today()->endOfDay();
            $start = (clone $end)->subDays($days - 1)->startOfDay();
        }

        // build a series of days in order
        $dates = collect();
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dates->push($d->format('Y-m-d'));
        }

        // aggregate collectable_amount per day (based on created_date or created_at)
        $rows = CourierPaidInvoice::query()
            // choose column storing invoice date. If you use 'created_date' (your schema), use that.
            ->selectRaw("date(created_date) as day, COALESCE(SUM(collectable_amount),0) as total")
            ->whereBetween('created_date', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day'); // keyed by day

        // map days to values (0 if missing)
        return $dates->map(function ($day) use ($rows) {
            return [
                'label' => $day,
                'value' => (float) ($rows[$day] ?? 0),
            ];
        });
    }

    /**
     * Get collectable amount grouped by month for given year (last 12 months if year is null).
     * Returns collection of ['label' => '2025-09', 'value' => 1234.56]
     */
    public function collectableAmountByMonths(?int $year = null): Collection
    {
        if ($year) {
            $start = Carbon::createFromDate($year, 1, 1)->startOfMonth();
            $end = (clone $start)->endOfYear()->endOfMonth();
        } else {
            // last 12 months including current month
            $end = Carbon::now()->endOfMonth();
            $start = (clone $end)->subMonths(11)->startOfMonth();
        }

        // build months sequence (YYYY-MM)
        $months = collect();
        for ($m = $start->copy(); $m->lte($end); $m->addMonth()) {
            $months->push($m->format('Y-m'));
        }

        $rows = CourierPaidInvoice::query()
            ->selectRaw("DATE_FORMAT(created_date, '%Y-%m') as ym, COALESCE(SUM(collectable_amount),0) as total")
            ->whereBetween('created_date', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        return $months->map(fn($ym) => ['label' => $ym, 'value' => (float) ($rows[$ym] ?? 0)]);
    }

    /**
     * Confirmed orders quantity in a date range (sum quantity)
     */
    public function confirmedOrderQuantity(?string $from = null, ?string $to = null): int
    {
        $q = Order::query();

        if ($from) $q->whereDate('order_date', '>=', $from);
        if ($to)   $q->whereDate('order_date', '<=', $to);

        return (int) $q->sum('quantity');
    }

    /**
     * Sum of order quantities where courier_paid_invoices.invoice_type = 'return'
     */
    public function paidInvoiceReturnQuantity(?string $from = null, ?string $to = null): int
    {
        $q = CourierPaidInvoice::query()
            ->where('invoice_type', PaidInvoiceType::TYPE_RETURN)
            ->join('orders', 'orders.id', '=', 'courier_paid_invoices.order_id')
            ->selectRaw('COALESCE(SUM(orders.quantity),0) as total');

        if ($from) $q->whereDate('courier_paid_invoices.created_date', '>=', $from);
        if ($to)   $q->whereDate('courier_paid_invoices.created_date', '<=', $to);

        return (int) $q->value('total') ?? 0;
    }

    /**
     * Sum of order quantities where courier_paid_invoices.invoice_type = 'delivery'
     */
    public function paidInvoiceDeliveryQuantity(?string $from = null, ?string $to = null): int
    {
        $q = CourierPaidInvoice::query()
            ->where('invoice_type', PaidInvoiceType::TYPE_DELIVERY)
            ->join('orders', 'orders.id', '=', 'courier_paid_invoices.order_id')
            ->selectRaw('COALESCE(SUM(orders.quantity),0) as total');

        if ($from) $q->whereDate('courier_paid_invoices.created_date', '>=', $from);
        if ($to)   $q->whereDate('courier_paid_invoices.created_date', '<=', $to);

        return (int) $q->value('total') ?? 0;
    }
}
