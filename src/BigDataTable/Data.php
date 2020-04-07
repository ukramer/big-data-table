<?php declare(strict_types=1);

namespace BigDataTable;

use Exception;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Data Object which can hold multiple records (over a long term date period).
 *
 * Literally a Data object is representing a row in the table and holds many
 * different Record objects.
 *
 * @package BigDataTable
 * @since 1.0.0
 */
abstract class Data extends Group implements JsonSerializable
{
    const SUM_TYPE_SUM = 0;
    const SUM_TYPE_LAST = 1;
    const SUM_TYPE_AVG = 2;
    /**
     * @var array
     */
    protected $options = [
        'cssClass' => '',
        'invertColor' => false,
    ];
    /**
     * @var int type of sum
     */
    private $sumType = self::SUM_TYPE_SUM;
    /**
     * @var Record[][][][]
     */
    private $records = [];

    /**
     * @return int
     */
    public function getSumType(): int
    {
        return $this->sumType;
    }

    /**
     * @param int $sumType
     */
    public function setSumType(int $sumType): void
    {
        $this->sumType = $sumType;
    }

    /**
     * @return Record[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    /**
     * @param Record[] $records
     */
    public function setRecords(array $records): void
    {
        $this->records = $records;
    }

    /**
     * @param Record $record
     */
    public function addRecord(Record $record): void
    {
        if (isset($this->records[$record->getYear()][$record->getMonth()][$record->getDay()][$record->getHour()])) {
            // skip as it is already part of data
            return;
        }
        $this->records[$record->getYear()][$record->getMonth()][$record->getDay()][$record->getHour()] = $record;
        $record->setData($this);
    }

    /**
     * @param Record $record
     */
    public function removeRecord(Record $record): void
    {
        if (!$this->hasRecord($record)) {
            throw new InvalidArgumentException('Record not part of Data');
        }
        $record->setData(null);
        unset($this->records[$record->getYear()][$record->getMonth()][$record->getDay()][$record->getHour()]);
    }

    /**
     * @param Record $record
     * @return bool
     */
    public function hasRecord(Record $record): bool
    {
        return isset($this->records[$record->getYear()][$record->getMonth()][$record->getDay()][$record->getHour()]);
    }

    /**
     * @inheritDoc
     */
    public function getSumByYear(int $year): int
    {
        switch ($this->sumType) {
            case self::SUM_TYPE_SUM:
                $sum = 0;
                /** @var Record $record */
                if (!empty($this->records)) {
                    foreach ($this->records[$year] as $monthRecords) {
                        foreach ($monthRecords as $dayRecords) {
                            foreach ($dayRecords as $record) {
                                $sum += $record->getValue();
                            }
                        }
                    }
                }
                return $sum;
                break;
            case self::SUM_TYPE_LAST:
                $lastRecord = $this->getLastRecordOfYear($year);
                return $lastRecord ? intval($lastRecord->getValue()) : 0;
                break;
            case self::SUM_TYPE_AVG:
                $sum = 0;
                $i = 0;
                /** @var Record $record */
                foreach ($this->records[$year] as $monthRecords) {
                    foreach ($monthRecords as $dayRecords) {
                        foreach ($dayRecords as $record) {
                            $i++;
                            $sum += $record->getValue();
                        }
                    }
                }
                return $i === 0 ? 0 : intval($sum / $i);
                break;
            default:
                throw new Exception('Sum type: ' . $this->sumType . ' not implemented');
                break;
        }
    }

    /**
     * Return the last record of a year.
     * This is used for data which is cumulative and where the data is a snapshot at a specific time.
     *
     * @param int $year
     * @return Record|null
     * @since 1.0.0
     */
    protected function getLastRecordOfYear(int $year): ?Record
    {
        // get value of last hour of last day of last month in year
        if (empty($this->records[$year])) {
            return null;
        }
        return end(end(end($this->records[$year])));
    }

    /**
     * @inheritDoc
     */
    public function getSumByYearAndMonth(int $year, int $month): int
    {
        switch ($this->sumType) {
            case self::SUM_TYPE_SUM:
                $sum = 0;
                if (!empty($this->records[$year][$month])) {
                    foreach ($this->records[$year][$month] as $dayRecords) {
                        foreach ($dayRecords as $record) {
                            $sum += $record->getValue();
                        }
                    }
                }
                return $sum;
            case self::SUM_TYPE_LAST:
                $lastRecord = $this->getLastRecordOfYearAndMonth($year, $month);
                return $lastRecord ? intval($lastRecord->getValue()) : 0;
                break;
            case self::SUM_TYPE_AVG:
                $sum = 0;
                $i = 0;
                /** @var Record $record */
                if (!isset($this->records[$year][$month])) {
                    return 0;
                }
                foreach ($this->records[$year][$month] as $dayRecords) {
                    foreach ($dayRecords as $record) {
                        $i++;
                        $sum += $record->getValue();
                    }
                }
                return $i === 0 ? 0 : intval($sum / $i);
                break;
            default:
                throw new Exception('Sum type: ' . $this->sumType . ' not implemented');
                break;
        }
    }

    /**
     * Return the last record of a year and month.
     * This is used for data which is cumulative and where the data is a snapshot at a specific time.
     *
     * @param int $year
     * @param int $month
     * @return Record|null
     * @since 1.0.0
     */
    protected function getLastRecordOfYearAndMonth(int $year, int $month): ?Record
    {
        // get value of last hour of last day of last month in year
        if (empty($this->records[$year][$month])) {
            return null;
        }
        return end(end($this->records[$year][$month]));
    }

    /**
     * Format value for display in the table.
     * This method gets overwritten by specific data objects.
     *
     * @param int $value The value as integer number.
     * @return string The value as string for display in the table.
     * @since 1.0.0
     */
    public function format(int $value): string
    {
        return (string)$value;
    }

    /**
     * @return bool TRUE if the Sum Type should be the average.
     * @since 1.0.0
     */
    public function isSumTypeAvg(): bool
    {
        return $this->sumType === self::SUM_TYPE_AVG;
    }

    /**
     * @return string Get the value of the option "cssClass".
     * @since 1.0.0
     */
    public function getCssClass(): string
    {
        return $this->options['cssClass'];
    }

    /**
     * @return bool Get the value of the option "invertColor".
     * @since 1.0.0
     */
    public function isInvertColor(): bool
    {
        return $this->options['invertColor'];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'records' => $this->records,
        ];
    }
}
