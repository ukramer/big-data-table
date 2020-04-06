<?php declare(strict_types=1);

namespace BigDataTable;

use JsonSerializable;

/**
 * This Parser class defines the necessary functionality that has to be implemented by a Parser.
 *
 * @package BigDataTable\Data
 * @since 1.0.0
 */
abstract class Parser implements JsonSerializable
{
    /**
     * Parse the table with all its data and return the resulting HTML output.
     *
     * @param Table $table
     * @return string
     * @since 1.0.0
     */
    abstract public function parse(Table $table): string;

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getName();
    }

    /**
     * Provides a readable name of the parser which is used for exception handling and logging.
     *
     * @return string
     */
    abstract function getName(): string;
}
