<?php

namespace App\Enums;

enum AppModelStatus: string
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    /**
     * Return all keys
     */
    public static function keys(): array
    {
        return array_map(fn($enum) => $enum->name, self::cases());
    }

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
