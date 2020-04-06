<?php declare(strict_types=1);

namespace BigDataTable;

use Exception;
use JsonSerializable;

/**
 * This Table class can have multiple data groups and a parser.
 *
 * The parser is necessary if a HTML should be printed out. If only JSON is wanted, there is no need for a parser.
 *
 * @package BigDataTable\Data
 * @since 1.0.0
 */
class Table implements JsonSerializable
{
    /**
     * @var DataGroup[]
     */
    private $dataGroups = [];
    /**
     * @var Parser|null
     */
    private $parser;
    /**
     * @var array
     */
    private $options = [
        'cssClass' => 'table table-condensed',
        'year' => 0, // default: current year
        'diffYear' => 0, // default: deactivated
        'showDiff' => false, // default: false (deactivated)
    ];

    /**
     * @param Parser|null $parser
     * @param array $options Possible options: cssClass (string, default "table table-condensed"), year (int, 0 = current year), diffYear (int, 0 = deactivated), showDiff (bool => deactivated)
     * @since 1.0.0
     */
    public function __construct(?Parser $parser = null, $options = [])
    {
        if ($parser) {
            $this->parser = $parser;
        }

        foreach ($options as $key => $option) {
            if (isset($this->options[$key])) {
                $this->options[$key] = $option;
            }
        }
    }

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

    /**
     * @param DataGroup $dataGroup
     */
    public function addDataGroup(DataGroup $dataGroup): void
    {
        $this->dataGroups[] = $dataGroup;
    }

    /**
     * @param DataGroup $dataGroup
     * @throws Exception
     */
    public function removeDataGroup(DataGroup $dataGroup): void
    {
        $i = array_search($dataGroup, $this->dataGroups);
        if ($i === false) {
            throw new Exception('Child to remove not found');
        }
        unset($this->dataGroups[$i]);
    }

    /**
     * @return Parser|null
     */
    public function getParser(): ?Parser
    {
        return $this->parser;
    }

    /**
     * @param Parser|null $parser
     */
    public function setParser(?Parser $parser): void
    {
        $this->parser = $parser;
    }

    /**
     * Gives the months that should be displayed in the table based on the options.
     *
     * @return array
     * @since 1.0.0
     */
    public function getMonths(): array
    {
        $monthCount = $this->options['year'] === 0 || $this->options['year'] === date('Y') ? date('m') : 12;
        $years = $this->getYears();
        $months = [];
        foreach (range($monthCount, 1) as $month) {
            foreach ($years as $year) {
                $months[] = [
                    'year' => $year,
                    'month' => $month,
                    'name' => date('M y', strtotime($year . '-' . $month . '-01')),
                ];
            }
        }
        return $months;
    }

    /**
     * Gives the years that should be displayed in the table based on the options.
     *
     * @return array
     * @since 1.0.0
     */
    public function getYears(): array
    {
        $years = [];
        if ($this->options['diffYear'] !== 0) {
            $years[] = $this->options['diffYear'];
        }
        $years[] = $this->options['year'] === 0 ? date('Y') : $this->options['year'];
        sort($years);
        return array_unique($years);
    }

    /**
     * Return the value of the option "diffYear".
     *
     * @return int
     * @since 1.0.0
     */
    public function getDiffYear(): int
    {
        return $this->options['diffYear'];
    }

    /**
     * Return the value of the option "showDiff".
     *
     * @return bool
     * @since 1.0.0
     */
    public function showDiff(): bool
    {
        return $this->options['showDiff'] && $this->options['diffYear'];
    }

    /**
     * Directly creates a new DataGroup and adds it to the data groups of the table.
     *
     * Alternatively a DataGroup can be created and added manually as DataGroup to the table object.
     *
     * @param string $title
     * @param string $description
     * @param array $options
     * @return DataGroup
     * @since 1.0.0
     */
    public function createGroup(string $title, string $description = '', array $options = []): DataGroup
    {
        $dataGroup = new DataGroup($title, $description, $options);
        $this->dataGroups[] = $dataGroup;
        return $dataGroup;
    }

    /**
     * Parse the table with help of the parser provided at instantiation.
     *
     * @return string
     * @throws Exception
     * @since 1.0.0
     */
    public function parse()
    {
        if (!$this->parser || !($this->parser instanceof Parser)) {
            throw new Exception('No Parser configured!');
        }
        return $this->parser->parse($this);
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
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->dataGroups;
    }
}
