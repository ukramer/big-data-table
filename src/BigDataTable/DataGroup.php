<?php declare(strict_types=1);

namespace BigDataTable;

class DataGroup extends Group implements \JsonSerializable
{
    /**
     * @var Group[]
     */
    private $childGroups;

    public function __construct($title, $description = '')
    {
        $this->title = $title;
        $this->description = $description;
    }

    public function addChild(Group $child): Group
    {
        $this->childGroups[] = $child;
        $child->setParent($this);
        return $child;
    }

    public function removeChild(Group $child): void
    {
        $i = array_search($child, $this->childGroups);
        if ($i === false) {
            throw new \Exception('Child to remove not found');
        }
        $this->childGroups[$i]->setParent(null);
        unset($this->childGroups[$i]);
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
