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

    case EXPENSE_TYPE_CREATE = 'expense_type.create';
    case EXPENSE_TYPE_READ   = 'expense_type.read';
    case EXPENSE_TYPE_UPDATE = 'expense_type.update';
    case EXPENSE_TYPE_DELETE = 'expense_type.delete';

    case EXPENSE_CREATE = 'expense.create';
    case EXPENSE_READ   = 'expense.read';
    case EXPENSE_UPDATE = 'expense.update';
    case EXPENSE_DELETE = 'expense.delete';

    case INCOME_TYPE_CREATE = 'income_type.create';
    case INCOME_TYPE_READ   = 'income_type.read';
    case INCOME_TYPE_UPDATE = 'income_type.update';
    case INCOME_TYPE_DELETE = 'income_type.delete';

    case INCOME_CREATE = 'income.create';
    case INCOME_READ   = 'income.read';
    case INCOME_UPDATE = 'income.update';
    case INCOME_DELETE = 'income.delete';

    case REPORT_EMPLOYEE_COMMISSION = 'report.employee_commission';
    case REPORT_EXPENSE = 'report.expense';
    case REPORT_INCOME = 'report.income';


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

    public static function newOwnerPermissions(): array
    {
        $excluded = [];

        return collect(self::values())
            ->reject(fn($permission) => in_array($permission, $excluded, true))
            ->values()
            ->toArray();
    }

    public static function newModeratorPermissions(): array
    {
        $permissions = [
            self::ORDER_READ,
            self::ORDER_CREATE,
            self::ORDER_UPDATE,
            self::ORDER_DELETE,
        ];

        return array_map(fn($permission) => $permission->value, $permissions);
    }
}
