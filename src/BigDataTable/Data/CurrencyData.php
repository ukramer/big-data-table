<?php declare(strict_types=1);

namespace BigDataTable\Data;

use BigDataTable\Data;

/**
 * CurrencyData is used to parse currency data with thousands separator.
 *
 * @package BigDataTable\Data
 * @since 1.0.0
 */
class CurrencyData extends Data
{
    /**
     * @inheritDoc
     */
    public static function format(int $value): string
    {
        return number_format($value, 0, '', '.');
    }
}
