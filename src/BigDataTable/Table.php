<?php declare(strict_types=1);

namespace BigDataTable;

class Table implements \JsonSerializable
{
    /**
     * @var DataGroup[]
     */
    private $dataGroups = [];
    /**
     * @var Parser|null
     */
    private $parser;

    private $options = [
        'cssClass' => 'table table-condensed',
        'year' => 0, // default: current year
        'diffYear' => 0, // default: deactivated
        'showDiff' => false, // default: false (deactivated)
    ];

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

    public function getYears(): array
    {
        $years = [];
        if ($this->options['diffYear'] !== 0) {
            $years[] = $this->options['diffYear'];
        }
        $years[] = $this->options['year'] === 0 ? date('Y') : $this->options['year'];
        sort($years);
        return $years;
    }

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

    public function getDiffYear(): int
    {
        return $this->options['diffYear'];
    }

    public function showDiff(): bool
    {
        return $this->options['showDiff'] && $this->options['diffYear'];
    }

    public function createGroup(string $title, ?string $description = '', ?array $options = []): DataGroup
    {
        $dataGroup = new DataGroup($title, $description, $options);
        $this->dataGroups[] = $dataGroup;
        return $dataGroup;
    }

    public function parse()
    {
        if (!$this->parser) {
            throw new \Exception('No Parser configured!');
        }
        return $this->parser->parse($this);
    }

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
