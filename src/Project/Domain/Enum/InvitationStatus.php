<?php


namespace Overseer\Project\Domain\Enum;


use MyCLabs\Enum\Enum;

/**
 * @method static INVITED()
 * @method static ACCEPTED()
 * @method static DECLINED()
 */
class InvitationStatus extends Enum
{
    private const INVITED = 'invited';
    private const ACCEPTED = 'accepted';
    private const DECLINED = 'declined';
}