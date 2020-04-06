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
        $nextYearValue = $this->getSumByYear($nextYear);
        $rate = 1;
        if ($nextYear == date('Y')) {
            $rate = 1 / (date('z') / 365);
        }
        $nextYearValue *= $rate;
        if ($prevYearValue === 0) {
            return 0;
        }
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
        $nextYearValue = $this->getSumByYearAndMonth($nextYear, $month);
        $rate = 1;
        if ($nextYear == date('Y') && $month == date('m')) {
            $rate = 1 / (date('d') / date('t'));
        }
        $nextYearValue *= $rate;
        if ($prevYearValue === 0) {
            return 0;
        }
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
}
