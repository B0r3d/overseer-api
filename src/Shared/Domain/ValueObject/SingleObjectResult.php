<?php


namespace Overseer\Shared\Domain\ValueObject;


use Overseer\Shared\Domain\Bus\Query\Result;

class SingleObjectResult implements Result
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}