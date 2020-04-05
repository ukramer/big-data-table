<?php declare(strict_types=1);

namespace BigDataTable;

abstract class Data extends Group implements \JsonSerializable
{
    /**
     * @var bool type of sum
     */
    private $cumulativeSum = false;

    /**
     * @var Record[][]
     */
    private $records = [];

    public function __construct($title, $description = '')
    {
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isCumulativeSum(): bool
    {
        return $this->cumulativeSum;
    }

    /**
     * @param bool $cumulativeSum
     */
    public function setCumulativeSum(bool $cumulativeSum)
    {
        $this->cumulativeSum = $cumulativeSum;
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
        if (isset($this->records[$record->getDateString()][$record->getHourString()])) {
            // skip as it is already part of data
            return;
        }
        $this->records[$record->getDateString()][$record->getHourString()] = $record;
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
        unset($this->records[$record->getDateString()][$record->getHourString()]);
    }

    /**
     * @param Record $record
     * @return bool
     */
    public function hasRecord(Record $record): bool
    {
        return isset($this->records[$record->getDateString()][$record->getHourString()]);
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
