<?php declare(strict_types=1);

namespace BigDataTable;

use \DateTime;

class Record implements \JsonSerializable
{
    /**
     * @var Data
     */
    private $data;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $value;

    public function __construct(DateTime $date, int $value, ?Data $data = null)
    {
        $this->date = $date;
        $this->value = $value;
        if ($data) {
            $data->addRecord($this);
        }
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @param Data $data
     */
    public function setData(?Data $data): void
    {
        $this->data = $data;
        $data->addRecord($this);
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return intval($this->date->format('Y'));
    }

    /**
     * @return int
     */
    public function getMonth(): int
    {
        return intval($this->date->format('m'));
    }

    /**
     * @return int
     */
    public function getDay(): int
    {
        return intval($this->date->format('d'));
    }

    /**
     * @return int
     */
    public function getHour(): int
    {
        return intval($this->date->format('H'));
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}
