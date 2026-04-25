<?php

use App\Models\ItemAttribute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

function get_pagination_summary($data)
{
    $total_item = $data->total();
    $item_per_page = $data->perPage();
    $current_page = $data->currentPage();

    $pagination_summary = "";
    if ($total_item > $item_per_page) {
        if ($current_page == 1) {
            $pagination_summary = "Showing 1 to $item_per_page records of $total_item";
        } else {
            if (($total_item - $current_page * $item_per_page) > $item_per_page) {
                $from = ($current_page - 1) * $item_per_page + 1;
                $to = $current_page * $item_per_page;
                $pagination_summary = "Showing $from to $to records of $total_item";
            } else {
                $from = ($current_page - 1) * $item_per_page + 1;
                $to = ($total_item - ($current_page - 1) * $item_per_page) + ($current_page - 1) * $item_per_page;
                $pagination_summary = "Showing $from to $to records of $total_item";
            }
        }
    }
    return $pagination_summary;
}


function log_exception(Throwable $e, string $context, array $extra = []): void
{
    $file = $e->getFile();
    $line = $e->getLine();

    $codeLine = null;
    if (is_readable($file)) {
        $lines = file($file);
        $codeLine = $lines[$line - 1] ?? null;
    }

    Log::error($context, [
        'exception' => class_basename($e),
        'message'   => $e->getMessage(),
        'file'      => $file,
        'line'      => $line,
        'code'      => trim((string) $codeLine),
        'trace'     => collect($e->getTrace())->take(10)->all(), // limit noise
        'extra'     => $extra,
    ]);
}


function excelDateToDateTimeString($excel_date)
{
    if (is_numeric($excel_date)) {
        $excel_date = Carbon::instance(
            Date::excelToDateTimeObject($excel_date)
        )->format('Y-m-d H:i:s');
    } else {
        $excel_date = Carbon::parse($excel_date)->format('Y-m-d H:i:s');
    }

    return $excel_date;
}

function parseItemAttribute($key, $value)
{
    $a = explode('_', $key);
    $item_attribute = ItemAttribute::find($a[1], ['label', 'type', 'options', 'is_required'])->toArray();
    $item_attribute['key'] = $key;
    $item_attribute['value'] = $value;
    return $item_attribute;
}
