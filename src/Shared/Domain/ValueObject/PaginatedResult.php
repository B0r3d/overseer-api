<?php


namespace Overseer\Shared\Domain\ValueObject;


use Overseer\Shared\Domain\Bus\Query\Result;

class PaginatedResult implements Result, \JsonSerializable
{
    protected int $page;
    protected int $count;
    protected array $items;

    public function __construct(array $items, int $count, int $page = 1)
    {
        $this->page = $page;
        $this->count = $count;
        $this->items = $items;
    }

    public function items()
    {
        return $this->items;
    }

    public function count()
    {
        return $this->count;
    }

    public function jsonSerialize()
    {
        return [
            'page' => $this->page,
            'count' => $this->count,
            'items' => $this->items,
        ];
    }
}