<?php declare(strict_types=1);

namespace BigDataTable\Data;

use BigDataTable\Data;

/**
 * PercentageData is used to parse percentage amounts with a % behind the values.
 *
 * @package BigDataTable\Data
 * @since 1.0.0
 */
class PercentageData extends Data
{
    /**
     * @inheritDoc
     */
    public function format(int $value): string
    {
        return parent::format($value) . '%';
    }

    /**
     * @inheritDoc
     */
    public function getDiffOfYear(int $prevYear, int $nextYear): int
    {
        $prevYearValue = $this->getSumByYear($prevYear);
        $nextYearValue = $this->getSumByYear($nextYear);
        $rate = 1;
        if ($nextYear == date('Y')) {
            $rate = 1 / (date('z') / 365);
        }
        $nextYearValue *= $rate;
        if ($prevYearValue === 0) {
            return 0;
        }
        return intval($nextYearValue - $prevYearValue);
    }

    /**
     * @inheritDoc
     */
    public function getDiffOfMonth(int $prevYear, int $nextYear, int $month): int
    {
        $prevYearValue = $this->getSumByYearAndMonth($prevYear, $month);
        $nextYearValue = $this->getSumByYearAndMonth($nextYear, $month);
        $rate = 1;
        if ($nextYear == date('Y') && $month == date('m')) {
            $rate = 1 / (date('d') / date('t'));
        }
        $nextYearValue *= $rate;
        if ($prevYearValue === 0) {
            return 0;
        }
        return intval($nextYearValue - $prevYearValue);
    }
}
