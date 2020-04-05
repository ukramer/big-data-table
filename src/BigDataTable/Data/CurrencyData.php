<?php declare(strict_types=1);

namespace BigDataTable\Data;

use BigDataTable\Data;

class CurrencyData extends Data
{
    public function format(int $value): string
    {
        return number_format($value, 0, '', '\'');
    }
}
