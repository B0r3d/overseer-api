<?php


namespace Overseer\Integration\Domain\Entity;


use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Shared\Domain\ValueObject\Uuid;

abstract class Integration
{
    protected string $id;
    protected IntegrationId $_id;
    protected string $projectId;
    protected Uuid $_projectId;
    protected \DateTime $createdAt;
    protected $messages;

    public function addMessage(IntegrationMessage $message)
    {
        $this->messages[] = $message;
    }

    public function __construct(IntegrationId $id, Uuid $projectId)
    {
        $this->id = $id->value();
        $this->_id = $id;
        $this->projectId = $projectId->value();
        $this->createdAt = new \DateTime();
        $this->messages = [];
    }

    public function getId(): IntegrationId
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        $this->_id = IntegrationId::fromString($this->id);
        return $this->_id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getProjectId(): Uuid
    {
        if (isset($this->_projectId)) {
            return $this->_projectId;
        }

        $this->_projectId = Uuid::fromString($this->projectId);
        return $this->_projectId;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}