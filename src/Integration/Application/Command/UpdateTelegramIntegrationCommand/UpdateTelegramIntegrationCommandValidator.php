<?php


namespace Overseer\Integration\Application\Command\UpdateTelegramIntegrationCommand;


use Overseer\Integration\Domain\Command\UpdateTelegramIntegrationCommand;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\Validator\ValidTelegramIntegration;
use Overseer\Integration\Domain\ValueObject\IntegrationId;
use Overseer\Project\Domain\Enum\ProjectMemberPermission;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Validator\AuthorizedProjectMember;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;

class UpdateTelegramIntegrationCommandValidator implements CommandValidator
{
    private ProjectReadModel $projectReadModel;
    private TelegramIntegrationReadModel $telegramIntegrationReadModel;

    public function __construct(ProjectReadModel $projectReadModel, TelegramIntegrationReadModel $telegramIntegrationReadModel)
    {
        $this->projectReadModel = $projectReadModel;
        $this->telegramIntegrationReadModel = $telegramIntegrationReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof UpdateTelegramIntegrationCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . UpdateTelegramIntegrationCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getId(), 'Invalid integration ID', [
                new ValidUuid(),
                new ValidTelegramIntegration($this->telegramIntegrationReadModel)
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }

        $integration = $this->telegramIntegrationReadModel->findById(IntegrationId::fromString($command->getId()));
        $projectId = $integration->getProjectId();
        $project = $this->projectReadModel->findById(ProjectId::fromString($projectId->value()));

        $validationContext = new ValidationContext([
            new Field($command->getIssuedBy(), 'Unauthorized request', [
                new AuthorizedProjectMember($project, ProjectMemberPermission::MANAGE_TELEGRAM_INTEGRATION()),
            ]),
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}