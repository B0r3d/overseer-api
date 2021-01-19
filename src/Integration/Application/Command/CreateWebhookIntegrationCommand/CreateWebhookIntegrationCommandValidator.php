<?php


namespace Overseer\Integration\Application\Command\CreateWebhookIntegrationCommand;


use Overseer\Integration\Domain\Command\CreateWebhookIntegrationCommand;
use Overseer\Project\Domain\Enum\ProjectMemberPermission;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Validator\AuthorizedProjectMember;
use Overseer\Project\Domain\Validator\ValidProject;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\Url;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;

class CreateWebhookIntegrationCommandValidator implements CommandValidator
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof CreateWebhookIntegrationCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . CreateWebhookIntegrationCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getProjectId(), 'Invalid project ID', [
                new ValidUuid(),
                new ValidProject($this->projectReadModel)
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));

        $validationContext = new ValidationContext([
            new Field($command->getIssuedBy(), 'Unauthorized request', [
                new AuthorizedProjectMember($project, ProjectMemberPermission::MANAGE_WEBHOOK_INTEGRATION())
            ]),
            new Field($command->getId(), 'Invalid ID', [
                new ValidUuid()
            ]),
            new Field($command->getUrl(), 'Bot ID cannot be blank', [
                new Url()
            ]),
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}