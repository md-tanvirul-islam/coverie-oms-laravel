<?php

namespace App\Enums;

enum StoreType: string
{
    case PRODUCT = 'PRODUCT';
    case SERVICE = 'SERVICE';
    case DIGITAL = 'DIGITAL';
    case MIXED = 'MIXED';

    /**
     * Return all values
     */
    public static function values(): array
    {
        return array_map(fn($enum) => $enum->value, self::cases());
    }

    /**
     * Return key => value pairs
     */
    public static function options(): array
    {
        return array_combine(
            array_map(fn($enum) => $enum->name, self::cases()),
            array_map(fn($enum) => $enum->value, self::cases())
        );
    }
}
