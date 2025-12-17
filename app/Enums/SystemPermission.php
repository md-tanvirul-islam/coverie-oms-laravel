<?php

namespace App\Enums;

enum SystemPermission: string
{
    case USER_CREATE = 'user.create';
    case USER_READ   = 'user.read';
    case USER_UPDATE = 'user.update';
    case USER_DELETE = 'user.delete';

    case ROLE_CREATE = 'role.create';
    case ROLE_READ   = 'role.read';
    case ROLE_UPDATE = 'role.update';
    case ROLE_DELETE = 'role.delete';

    case STORE_CREATE = 'store.create';
    case STORE_READ   = 'store.read';
    case STORE_UPDATE = 'store.update';
    case STORE_DELETE = 'store.delete';

    case ORDER_CREATE = 'order.create';
    case ORDER_READ   = 'order.read';
    case ORDER_UPDATE = 'order.update';
    case ORDER_DELETE = 'order.delete';

    case COURIER_PAID_INVOICE_CREATE = 'courier_paid_invoice.create';
    case COURIER_PAID_INVOICE_READ   = 'courier_paid_invoice.read';
    case COURIER_PAID_INVOICE_UPDATE = 'courier_paid_invoice.update';
    case COURIER_PAID_INVOICE_DELETE = 'courier_paid_invoice.delete';

    case REPORT_MODERATOR_COMMISSION = 'report.moderator_commission';


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

    /**
     * Return permissions by module
     */
    public static function byModule(string $module): array
    {
        return array_filter(self::values(), fn($perm) => str_starts_with($perm, strtolower($module) . '.'));
    }
}
