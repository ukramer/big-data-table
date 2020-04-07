<?php declare(strict_types=1);

namespace BigDataTable\Data;

use BigDataTable\Data;

/**
 * ScalarData is used to parse integer amounts.
 *
 * There is currently no special functionality needed than the functionality that is provided by Data.
 * @see Data
 *
 * @package BigDataTable\Data
 * @since 1.0.0
 */
class ScalarData extends Data
{
    /**
     * @inheritDoc
     */
    public static function format(int $value): string
    {
        return number_format($value, 0, '', '\'');
    }
}
