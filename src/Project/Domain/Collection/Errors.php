<?php


namespace Overseer\Project\Domain\Collection;


use Overseer\Project\Domain\Entity\Error;
use Overseer\Project\Domain\ValueObject\ErrorId;

class Errors extends \ArrayObject
{
    public function findById(ErrorId $errorId): ?Error
    {
        /** @var Error $item */
        foreach ($this as $item) {
            if ($item->getId()->equals($errorId)) {
                return $item;
            }
        }

        return null;
    }
}