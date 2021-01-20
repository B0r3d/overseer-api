<?php


namespace Overseer\Integration\Domain\Enum;


use MyCLabs\Enum\Enum;

/**
 * @method static UNPROCESSED()
 * @method static PROCESSED()
 * @method static ERROR()
 */
class MessageStatus extends Enum
{
    private const UNPROCESSED = 'unprocessed';
    private const PROCESSED = 'processed';
    private const ERROR = 'error';
}