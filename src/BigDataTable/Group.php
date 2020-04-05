<?php declare(strict_types=1);

namespace BigDataTable;

abstract class Group
{
    /**
     * @var DataGroup
     */
    protected $parent;
    protected $title;
    protected $description;

    /**
     * @var array
     */
    protected $options = [];

    public function __construct(string $title, string $description = '', ?array $options = [])
    {
        $this->title = $title;
        $this->description = $description;

        foreach ($options as $key => $option) {
            if (isset($this->options[$key])) {
                $this->options[$key] = $option;
            }
        }
    }

    abstract public function getSumByYear(int $year): int;
    abstract public function getSumByYearAndMonth(int $year, int $month): int;

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

    public function getParent(): ?DataGroup
    {
        return $this->parent;
    }

    public function setParent(?DataGroup $parent): void
    {
        $this->parent = $parent;
    }


    public function getDiffOfYear(int $prevYear, int $nextYear): int
    {
        $prevYearValue = $this->getSumByYear($prevYear);
        $nextYearValue = $this->getSumByYear($nextYear);
        $rate = 1;
        if ($nextYear == date('Y')) {
            $rate = 1/(date('z')/365);
        }
        $nextYearValue *= $rate;
        $diff = intval($nextYearValue / $prevYearValue * 100);
        return $diff - 100;
    }

    public function getDiffOfMonth(int $prevYear, int $nextYear, int $month): int
    {
        $prevYearValue = $this->getSumByYearAndMonth($prevYear, $month);
        $nextYearValue = $this->getSumByYearAndMonth($nextYear, $month);
        $rate = 1;
        if ($nextYear == date('Y') && $month == date('m')) {
            $rate = 1/(date('d')/date('t'));
        }
        $nextYearValue *= $rate;
        $diff = intval($nextYearValue / $prevYearValue * 100);
        return $diff - 100;
    }
}
