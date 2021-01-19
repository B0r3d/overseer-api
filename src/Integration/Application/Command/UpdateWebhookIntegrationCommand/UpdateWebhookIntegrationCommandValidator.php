<?php


namespace Overseer\Integration\Application\Command\UpdateWebhookIntegrationCommand;


use Overseer\Integration\Domain\Command\UpdateWebhookCommand;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\Validator\ValidWebhookIntegration;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Project\Domain\Enum\ProjectMemberPermission;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Validator\AuthorizedProjectMember;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\Url;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;

class UpdateWebhookIntegrationCommandValidator implements CommandValidator
{
    private ProjectReadModel $projectReadModel;
    private WebhookIntegrationReadModel $webhookIntegrationModel;

    public function __construct(ProjectReadModel $projectReadModel, WebhookIntegrationReadModel $webhookIntegrationModel)
    {
        $this->projectReadModel = $projectReadModel;
        $this->webhookIntegrationModel = $webhookIntegrationModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof UpdateWebhookCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . UpdateWebhookCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getId(), 'Invalid integration ID', [
                new ValidUuid(),
                new ValidWebhookIntegration($this->webhookIntegrationModel)
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }

        $integration = $this->webhookIntegrationModel->findById(IntegrationId::fromString($command->getId()));
        $projectId = $integration->getProjectId();
        $project = $this->projectReadModel->findById(ProjectId::fromString($projectId->value()));

        $validationContext = new ValidationContext([
            new Field($command->getIssuedBy(), 'Unauthorized request', [
                new AuthorizedProjectMember($project, ProjectMemberPermission::MANAGE_WEBHOOK_INTEGRATION()),
            ]),
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }

        if ($command->getUrl()) {
            $validationContext = new ValidationContext([
                new Field($command->getUrl(), 'Invalid URL', [
                    new Url()
                ])
            ]);

            if (!$validationContext->isValid()) {
                throw new ValidationException($validationContext->getErrorMessage());
            }
        }
    }
}