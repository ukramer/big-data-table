<?php declare(strict_types=1);

namespace BigDataTable;

class Table implements \JsonSerializable
{
    /**
     * @var DataGroup[]
     */
    private $dataGroups = [];

    /**
     * @return DataGroup[]
     */
    public function getDataGroups(): array
    {
        return $this->dataGroups;
    }

    /**
     * @param DataGroup[] $dataGroups
     */
    public function setDataGroups(array $dataGroups): void
    {
        $this->dataGroups = $dataGroups;
    }

    public function addDataGroup(DataGroup $dataGroup): void
    {
        $this->dataGroups[] = $dataGroup;
    }

    public function removeDataGroup(DataGroup $dataGroup): void
    {
        $i = array_search($dataGroup, $this->dataGroups);
        if ($i === false) {
            throw new \Exception('Child to remove not found');
        }
        unset($this->dataGroups[$i]);
    }

    public function createGroup($title, $description = ''): DataGroup
    {
        $dataGroup = new DataGroup($title, $description);
        $this->dataGroups[] = $dataGroup;
        return $dataGroup;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->dataGroups;
    }
}
