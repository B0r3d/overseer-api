<?php


namespace Overseer\Shared\Domain\ValueObject;


use Overseer\Shared\Domain\Bus\Query\Query;

class PaginatedQuery implements Query
{
    protected array $criteria;
    protected array $sort;
    protected int $page;

    public function __construct(int $page = 1, array $criteria = [], array $sort = [])
    {
        $this->criteria = $criteria;
        $this->sort = $sort;
        $this->page = $page;
    }


    public function criteria(): array
    {
        return $this->criteria;
    }

    public function sort(): array
    {
        return $this->sort;
    }

    public function page(): int
    {
        return $this->page;
    }
}