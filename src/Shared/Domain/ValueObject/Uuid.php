<?php


namespace Overseer\Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    protected string $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);

        $this->value = $value;
    }

    public static function fromString(string $value)
    {
        return new static($value);
    }

    public static function random(): self
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Uuid $other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    private function ensureIsValidUuid($id): void
    {
        if (!RamseyUuid::isValid($id)) {
            throw new \InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $id));
        }
    }
}