<?php

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
