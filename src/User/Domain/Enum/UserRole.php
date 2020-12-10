<?php


namespace Overseer\User\Domain\Enum;


use MyCLabs\Enum\Enum;

/**
 * @method static UserRole USER()
 * @method static UserRole ADMIN()
 */
final class UserRole extends Enum
{
    private const USER = 'ROLE_USER';
    private const ADMIN = 'ROLE_ADMIN';
    private const SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}