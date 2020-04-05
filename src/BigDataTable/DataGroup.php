<?php declare(strict_types=1);

namespace BigDataTable;

class DataGroup extends Group implements \JsonSerializable
{
    /**
     * @var Group[]
     */
    private $children;

    /**
     * @var array
     */
    protected $options = [
        'cssClass' => '',
        'sum' => false,
        'showPercentageDiff' => false,
    ];

    public function addChild(Group $child): Group
    {
        $this->children[] = $child;
        $child->setParent($this);
        return $child;
    }

    public function removeChild(Group $child): void
    {
        $i = array_search($child, $this->children);
        if ($i === false) {
            throw new \Exception('Child to remove not found');
        }
        $this->children[$i]->setParent(null);
        unset($this->children[$i]);
    }

    public function getSumByYear(int $year): int
    {
        $sum = 0;
        foreach ($this->getChildren() as $child) {
            $sum += $child->getSumByYear($year);
        }
        return $sum;
    }

    public function getSumByYearAndMonth(int $year, int $month): int
    {
        $sum = 0;
        foreach ($this->getChildren() as $child) {
            $sum += $child->getSumByYearAndMonth($year, $month);
        }
        return $sum;
    }

    /**
     * @return Group[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function createSubGroup($title, $description = ''): DataGroup
    {
        $dataGroup = new self($title, $description);
        $this->addChild($dataGroup);
        return $dataGroup;
    }

    public function addData(Data $data): Data
    {
        /** @var Data $ret */
        $ret = $this->addChild($data);
        return $ret;
    }

    public function getCssClass(): string
    {
        return $this->options['cssClass'];
    }

    public function isSumActive(): bool
    {
        return $this->options['sum'];
    }

    public function isShowPercentageDiffActive(): bool
    {
        return $this->options['showPercentageDiff'];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'children' => $this->childGroups,
        ];
    }
}
