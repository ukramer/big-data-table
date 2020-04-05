<?php declare(strict_types=1);

namespace BigDataTable;

abstract class Parser implements \JsonSerializable
{
    abstract function getName(): string;

    abstract public function parse(Table $table): string;

    public function jsonSerialize()
    {
        return $this->getName();
    }
}
