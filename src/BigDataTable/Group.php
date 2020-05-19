<?php declare(strict_types=1);

namespace BigDataTable;

use Exception;

/**
 * Group abstract class which defines the interface of a Group (Group of data or group of records).
 *
 * @package BigDataTable
 * @since 1.0.0
 */
abstract class Group
{
    /**
     * @var Table
     */
    protected $table;
    /**
     * @var DataGroup
     */
    protected $parent;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Construct a Group which can be a group of data or a group of records.
     *
     * @param string $title
     * @param string $description
     * @param array $options
     * @since 1.0.0
     */
    public function __construct(string $title, string $description = '', array $options = [])
    {
        $this->title = $title;
        $this->description = $description;

        foreach ($options as $key => $option) {
            if (isset($this->options[$key])) {
                $this->options[$key] = $option;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isGroup()
    {
        return $this instanceof DataGroup;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @param Table $table
     */
    public function setTable(Table $table): void
    {
        $this->table = $table;
    }

    /**
     * @return DataGroup|null
     */
    public function getParent(): ?DataGroup
    {
        return $this->parent;
    }

    /**
     * @param DataGroup|null $parent
     */
    public function setParent(?DataGroup $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Calculate the diff of the sums of a year.
     *
     * Get the value diff for percentages. This is literally a subtraction.
     * Example:
     * - 2019: 20%
     * - 2020: 40%
     * Diff: +20%
     *
     * @param int $prevYear some year before $nextYear
     * @param int $nextYear the year which we want the diff for
     * @return int
     * @throws Exception
     * @since 1.0.0
     */
    public function getDiffOfYear(int $prevYear, int $nextYear): int
    {
        $prevYearValue = $this->getSumByYear($prevYear);
        if ($prevYearValue === 0) {
            return 0;
        }
        $nextYearValue = $this->getForecastByYear($nextYear);
        $diff = intval($nextYearValue / $prevYearValue * 100);
        return $diff - 100;
    }

    /**
     * Return the sum of all records of a specific year.
     *
     * Depending on the sumType of the data object, it will return either:
     * SUM => the sum of all records
     * LAST => the value of the last record (used for data which are snapshots at a specific time)
     * AVG => the calculative average of the records
     *
     * @param int $year
     * @return int
     * @throws Exception
     * @since 1.0.0
     */
    abstract public function getSumByYear(int $year): int;

    /**
     * Return the forecast of the year period if it is current year.
     *
     * Depending on the sumType of the data object, it will return either:
     * SUM => the sum of all records
     * LAST => the value of the last record - the value of the last record of previous period
     * AVG => the calculative average of the records
     *
     * @param int $year
     * @return float
     * @throws Exception
     * @since 1.1.2
     */
    abstract public function getForecastByYear(int $year): float;

    /**
     * Return the growth during the year period.
     *
     * Depending on the sumType of the data object, it will return either:
     * SUM => the sum of all records
     * LAST => the value of the last record - the value of the last record of previous period
     * AVG => the calculative average of the records
     *
     * @param int $year
     * @return int
     * @throws Exception
     * @since 1.1.2
     */
    abstract public function getGrowthByYear(int $year): int;

    /**
     * Return the growth during the year and month specified.
     *
     * Depending on the sumType of the data object, it will return either:
     * SUM => the sum of all records
     * LAST => the value of the last record - the value of the last record of previous period
     * AVG => the calculative average of the records
     *
     * @param int $year
     * @param int $month
     * @return int
     * @throws Exception
     * @since 1.1.2
     */
    abstract public function getGrowthByYearAndMonth(int $year, int $month): int;

    /**
     * Calculate the diff of the sums of a month from two different years.
     *
     * Get the value diff for percentages. This is literally a subtraction.
     * Example:
     * - 2019: 20%
     * - 2020: 40%
     * Diff: +20%
     *
     * @param int $prevYear
     * @param int $nextYear
     * @param int $month
     * @return int
     * @throws Exception
     * @since 1.0.0
     */
    public function getDiffOfMonth(int $prevYear, int $nextYear, int $month): int
    {
        $prevYearValue = $this->getSumByYearAndMonth($prevYear, $month);
        if ($prevYearValue === 0) {
            return 0;
        }
        $nextYearValue = $this->getForecastByYearAndMonth($nextYear, $month);
        $diff = intval($nextYearValue / $prevYearValue * 100);
        return $diff - 100;
    }

    /**
     * Return the sum of all records of a specific month.
     *
     * Depending on the sumType of the data object, it will return either:
     * SUM => the sum of all records
     * LAST => the value of the last record (used for data which are snapshots at a specific time)
     * AVG => the calculative average of the records
     *
     * @param int $year
     * @param int $month
     * @return int
     * @throws Exception
     * @since 1.0.0
     */
    abstract public function getSumByYearAndMonth(int $year, int $month): int;

    /**
     * Return the forecast of the year and month period if it is current year and month
     *
     * Depending on the sumType of the data object, it will return either:
     * SUM => the sum of all records
     * LAST => the value of the last record - the value of the last record of previous period
     * AVG => the calculative average of the records
     *
     * @param int $year
     * @param int $month
     * @return float
     * @throws Exception
     * @since 1.1.2
     */
    abstract public function getForecastByYearAndMonth(int $year, int $month): float;

    /**
     * Calculate the rate which has to be applied to get the forecast for current period.
     *
     * @param int $year
     * @param int $month
     * @return float
     * @since 1.1.2
     */
    protected function getForecastRateByYearAndMonth(int $year, int $month): float
    {
        $rate = 1;
        if ($year == date('Y') && $month == date('m')) {
            $rate = 1 / (date('d') / date('t'));
        }
        return $rate;
    }

    /**
     * Calculate the rate which has to be applied to get the forecast for current period.
     *
     * @param int $year
     * @return float
     * @since 1.1.2
     */
    protected function getForecastRateByYear(int $year): float
    {
        $rate = 1;
        if ($year == date('Y')) {
            $rate = 1 / (date('z') / 365);
        }
        return $rate;
    }
}
