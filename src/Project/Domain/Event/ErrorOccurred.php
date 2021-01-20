<?php


namespace Overseer\Project\Domain\Event;


use Overseer\Shared\Domain\Bus\Event\DomainEvent;

final class ErrorOccurred extends DomainEvent
{
    private string $projectId;
    private string $errorId;

    static function eventName(): string
    {
        return 'error.occurred';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredAt): DomainEvent
    {
        return new self(
            $aggregateId,
            $body['error_id'],
            $eventId,
            $occurredAt
        );
    }

    public function toPrimitives(): array
    {
        return [
            'project_id' => $this->projectId,
            'error_id' => $this->errorId,
        ];
    }

    public function __construct(string $projectId, string $errorId, string $eventId = null, string $occurredAt = null)
    {
        $this->projectId = $projectId;
        $this->errorId = $errorId;

        parent::__construct($projectId, $eventId, $occurredAt);
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getErrorId(): string
    {
        return $this->errorId;
    }
}