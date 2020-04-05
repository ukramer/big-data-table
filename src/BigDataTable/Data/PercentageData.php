<?php declare(strict_types=1);

namespace BigDataTable\Data;

use BigDataTable\Data;

class PercentageData extends Data
{
    public function format(int $value): string
    {
        return parent::format($value) . '%';
    }
}
