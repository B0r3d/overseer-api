<?php


namespace Overseer\Integration\Domain\ValueObject;


class Filters
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function filters(): bool
    {
        return count($this->filters) > 0;
    }

    public function isFiltered(string $phrase): bool
    {
        foreach ($this->filters as $filter) {
            if (strpos($filter, $phrase) !== false || strpos($phrase, $filter)) {
                return true;
            }
        }

        return false;
    }
}