<?php


namespace Overseer\Shared\Infrastructure\Bus\Event;


use MyCLabs\Enum\Enum;

/**
 * @method static PROCESSED()
 * @method static FAILED()
 * @method static UNPROCESSED()
 */
class EventStatus extends Enum
{
    private const PROCESSED = 'processed';
    private const FAILED = 'failed';
    private const UNPROCESSED = 'unprocessed';
}