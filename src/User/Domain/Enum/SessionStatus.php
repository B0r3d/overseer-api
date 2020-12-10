<?php


namespace Overseer\User\Domain\Enum;


use MyCLabs\Enum\Enum;

/**
 * @method static SessionStatus VALID()
 * @method static SessionStatus TERMINATED()
 * @method static SessionStatus EXPIRED()
 */
final class SessionStatus extends Enum
{
    private const VALID = 'valid';
    private const TERMINATED = 'terminated';
    private const EXPIRED = 'expired';
}