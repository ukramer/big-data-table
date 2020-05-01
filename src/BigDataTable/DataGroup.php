<?php declare(strict_types=1);

namespace BigDataTable;

use BigDataTable\Data\ScalarData;
use DateTime as DateTime;
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
        'topView' => 0,
        'topViewOtherLabel' => 'Others',
        'sum' => false,
        'showPercentageDiff' => false,
        'sumDataTypeFormatter' => ScalarData::class,
    ];
    /**
     * @var Group[]
     */
    private $children = [];

    /**
     * DataGroup constructor.
     *
     * @param Table $table
     * @param string $title
     * @param string $description
     * @param array $options
     * @since 1.1.0
     */
    public function __construct(Table $table, string $title, string $description = '', array $options = [])
    {
        parent::__construct($title, $description, $options);
        $this->setTable($table);
    }

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
     * Get filtered children by options
     * - topView
     *
     * This will merge all the Data objects which have less total amount in the current year.
     * It will show the top X (X = topView value) of the group and will merge all others to "others"
     *
     * @param int $year
     * @return array
     * @throws Exception
     * @since 1.1.0
     */
    public function getDisplayedChildren(int $year): array
    {
        if (!$this->getTopView()) {
            return $this->getChildren();
        }

        /** @var Group[] $childrenBySum */
        $childrenBySum = [];
        foreach ($this->getChildren() as $child) {
            $childrenBySum[$child->getSumByYear($year)] = $child;
        }
        krsort($childrenBySum);

        $children = array_splice($childrenBySum, 0, $this->getTopView());

        if (empty($childrenBySum)) {
            return $children;
        }

        /**
         * @var Data $other
         */
        $other = clone current($childrenBySum);
        $other->setTitle($this->getTopViewOtherLabel());
        $other->setRecords([]);
        $children[] = $other;

        foreach ($this->getTable()->getMonths() as $month) {
            $year = intval($month['year']);
            $month = intval($month['month']);

            $date = new DateTime();
            $date->setDate($year, $month, 1);
            $date->setTime(0, 0, 0);
            $sum = $other->getSumByYearAndMonth($year, $month);
            foreach ($childrenBySum as $item) {
                $sum += $item->getSumByYearAndMonth($year, $month);
            }
            $other->addRecord(new Record($date, $sum));
        }
        return $children;
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
        $dataGroup = new self($this->table, $title, $description, $options);
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
     * Return the value of the option "topView".
     *
     * @return int
     * @since 1.1.0
     */
    public function getTopView(): int
    {
        if (!$this->options['topView']) {
            return $this->options['topView'];
        }
        foreach ($this->getChildren() as $child) {
            if (!($child instanceof Data)) {
                return 0;
            }
        }
        return $this->options['topView'];
    }

    /**
     * Return the value of the option "topViewOtherLabel".
     *
     * @return string
     * @since 1.1.0
     */
    public function getTopViewOtherLabel(): string
    {
        return $this->options['topViewOtherLabel'];
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
