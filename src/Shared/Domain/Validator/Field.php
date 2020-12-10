<?php


namespace Overseer\Shared\Domain\Validator;


class Field
{
    private $value;
    private string $errorMessage;
    private array $specifications;

    public function __construct($value, string $errorMessage = '', $specifications = [])
    {
        $this->value = $value;
        $this->errorMessage = $errorMessage;
        $this->specifications = $specifications;
    }

    public function isValid(): bool
    {
        /** @var Specification $specification */
        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($this->value)) {
                return false;
            }
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}