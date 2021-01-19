<?php


namespace Overseer\Shared\Domain\Validator;


class ValidationContext
{
    private array $fields;
    private string $errorMessage = '';

    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    public function isValid(): bool
    {
        /** @var Field $field */
        foreach ($this->fields as $field) {
            if (!$field->isValid()) {
                $this->errorMessage = $field->getErrorMessage();
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