<?php

namespace App\Enums;

enum PaidInvoiceType: string
{
    case TYPE_DELIVERY = 'delivery';
    case TYPE_RETURN = 'return';

    /**
     * Return all enum values
     */
    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }

    /**
     * Return key => value pairs (optional)
     */
    public static function options(): array
    {
        return array_combine(
            array_map(fn($enum) => $enum->name, self::cases()),
            array_map(fn($enum) => $enum->value, self::cases())
        );
    }
}
