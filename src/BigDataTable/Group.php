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

    public function setParent(?DataGroup $parent): void
    {
        $this->parent = $parent;
    }
}
