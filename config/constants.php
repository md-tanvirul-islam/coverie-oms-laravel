<?php

use App\Enums\CourierName;
use App\Enums\PaidInvoiceType;

return [
    'couriers' => CourierName::options(),

    'paid_invoice_types' => PaidInvoiceType::options(),

    'pagination' => [
        'per_page' => 15,
    ],
];
