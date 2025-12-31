<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelDate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_numeric($value)) {
            try {
                Date::excelToDateTimeObject($value);
            } catch (\Throwable $e) {
                $fail("Invalid Excel date value.");
            }
        } else {
            if (!strtotime($value)) {
                $fail("Invalid date format.");
            }
        }
    }
}
