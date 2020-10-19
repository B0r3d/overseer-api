<?php


namespace Overseer\Project\Domain\Exception;


use Overseer\Project\Domain\ValueObject\Slug;

class ProjectExistsException extends \RuntimeException
{
    public static function withSlug(Slug $slug)
    {
        $message = 'Project with slug "' . $slug->value() . '" already exists.';
        return new self($message);
    }
}