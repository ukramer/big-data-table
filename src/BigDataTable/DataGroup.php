<?php declare(strict_types=1);

namespace BigDataTable;

use BigDataTable\Data\ScalarData;
use Exception;
use JsonSerializable;

/**
 * DataGroup which holds multiple Data objects or DataGroups again.
 *
 * It is possible to have like a tree structure (as seen in directory structure of an operating system or
 * in webstores for the categories and sub-categories). Finally data objects can be children of this object.
 * Data objects contain the effective Records of data.
 *
 * @package BigDataTable
 * @since 1.0.0
 */
class DataGroup extends Group implements JsonSerializable
{
    /**
     * @var array
     */
    protected $options = [
        'cssClass' => '',
        'sum' => false,
        'showPercentageDiff' => false,
        'sumDataTypeFormatter' => ScalarData::class,
    ];
    /**
     * @var Group[]
     */
    private $children = [];

    /**
     * @param Group $child
     * @throws Exception
     */
    public function removeChild(Group $child): void
    {
        $i = array_search($child, $this->children);
        if ($i === false) {
            throw new Exception('Child to remove not found');
        }
        $this->children[$i]->setParent(null);
        unset($this->children[$i]);
    }

    /**
     * @inheritDoc
     */
    public function getSumByYear(int $year): int
    {
        $sum = 0;
        foreach ($this->getChildren() as $child) {
            $sum += $child->getSumByYear($year);
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

    /**
     * @inheritDoc
     */
    public function getSumByYearAndMonth(int $year, int $month): int
    {
        $sum = 0;
        foreach ($this->getChildren() as $child) {
            $sum += $child->getSumByYearAndMonth($year, $month);
        }
        return $sum;
    }

    /**
     * Shortcut for adding a new sub group.
     *
     * Instead of using this function, also addChild can be called directly with a new DataGroup object as parameter.
     *
     * @param $title
     * @param string $description
     * @param array $options
     * @return DataGroup
     * @since 1.0.0
     */
    public function createSubGroup(string $title, string $description = '', array $options = []): DataGroup
    {
        $dataGroup = new self($title, $description, $options);
        $this->addChild($dataGroup);
        return $dataGroup;
    }

    /**
     * @param Group $child
     * @return Group
     */
    public function addChild(Group $child): Group
    {
        $this->children[] = $child;
        $child->setParent($this);
        return $child;
    }

    /**
     * @param Data $data
     * @return Data
     */
    public function addData(Data $data): Data
    {
        /** @var Data $ret */
        $ret = $this->addChild($data);
        return $ret;
    }

    /**
     * Return the value of the option "cssClass".
     *
     * @return string
     * @since 1.0.0
     */
    public function getCssClass(): string
    {
        return $this->options['cssClass'];
    }

    /**
     * Return the value of the option "sum".
     *
     * @return bool
     * @since 1.0.0
     */
    public function isSumActive(): bool
    {
        return $this->options['sum'];
    }

    /**
     * Return the value of the option "showPercentageDiff".
     *
     * @return bool
     * @since 1.0.0
     */
    public function isShowPercentageDiffActive(): bool
    {
        return $this->options['showPercentageDiff'];
    }

    /**
     * Return the value of the option "sumDataTypeFormatter".
     *
     * @return string
     * @since 1.0.0
     */
    public function getSumDataTypeFormatter(): string
    {
        return $this->options['sumDataTypeFormatter'];
    }

    /**
     * Format the sums of a data group.
     *
     * @param int $value
     * @return string
     * @since 1.0.0
     */
    public function format(int $value): string
    {
        if (class_exists($this->getSumDataTypeFormatter())) {
            $function = $this->getSumDataTypeFormatter() . '::format';
            $value = call_user_func($function, $value);
        }
        return (string)$value;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'children' => $this->children,
        ];
    }
}
