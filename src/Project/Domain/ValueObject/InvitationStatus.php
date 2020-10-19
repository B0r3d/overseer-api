<?php


namespace Overseer\Project\Domain\ValueObject;


use Overseer\Shared\Domain\ValueObject\StringValueObject;

final class InvitationStatus extends StringValueObject
{
    const INVITED = 'invited';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';

    public static function getAllowedOptions(): array
    {
        return [
            self::INVITED,
            self::ACCEPTED,
            self::REJECTED
        ];
    }

    public function __construct(string $value)
    {
        if (!in_array($value, self::getAllowedOptions())) {
            throw new \InvalidArgumentException('Got "' . $value . '", allowed options are' . join(', ', self::getAllowedOptions()));
        }

        parent::__construct($value);
    }
}