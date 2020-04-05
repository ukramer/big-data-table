<?php declare(strict_types=1);

namespace BigDataTable;

abstract class Data extends Group implements \JsonSerializable
{
    const SUM_TYPE_SUM = 0;
    const SUM_TYPE_LAST = 1;
    const SUM_TYPE_AVG = 2;

    /**
     * @var int type of sum
     */
    private $sumType = self::SUM_TYPE_SUM;

    /**
     * @var array
     */
    protected $options = [
        'cssClass' => '',
    ];

    /**
     * @var Record[][]
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
            throw new \InvalidArgumentException('Record not part of Data');
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

    public function getSumByYear(int $year): int
    {
        switch ($this->sumType) {
            case self::SUM_TYPE_SUM:
                $sum = 0;
                /** @var Record $record */
                foreach ($this->records[$year] as $monthRecords) {
                    foreach ($monthRecords as $dayRecords) {
                        foreach ($dayRecords as $record) {
                            $sum += $record->getValue();
                        }
                    }
                }
                return $sum;
                break;
            case self::SUM_TYPE_LAST:
                return intval($this->getLastRecordOfYear($year)->getValue());
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
                throw new \Exception('Sum type: ' . $this->sumType . ' not implemented');
                break;
        }
    }

    protected function getLastRecordOfYear(int $year): ?Record
    {
        // get value of last hour of last day of last month in year
        return end(end(end($this->records[$year])));
    }

    public function getSumByYearAndMonth(int $year, int $month): int
    {
        switch ($this->sumType) {
            case self::SUM_TYPE_SUM:
                $sum = 0;
                foreach ($this->records[$year][$month] as $dayRecords) {
                    foreach ($dayRecords as $record) {
                        $sum += $record->getValue();
                    }
                }
                return $sum;
            case self::SUM_TYPE_LAST:
                return intval($this->getLastRecordOfYearAndMonth($year, $month)->getValue());
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
                throw new \Exception('Sum type: ' . $this->sumType . ' not implemented');
                break;
        }
    }

    protected function getLastRecordOfYearAndMonth(int $year, int $month): ?Record
    {
        // get value of last hour of last day of last month in year
        return end(end($this->records[$year][$month]));
    }

    public function format(int $value): string
    {
        return (string)$value;
    }

    public function getCssClass(): string
    {
        return $this->options['cssClass'];
    }

    public function isSumTypeAvg(): bool
    {
        return $this->sumType === self::SUM_TYPE_AVG;
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
