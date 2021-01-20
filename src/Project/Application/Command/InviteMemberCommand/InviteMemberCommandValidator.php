<?php


namespace Overseer\Project\Application\Command\InviteMemberCommand;


use Overseer\Project\Domain\Command\InviteMemberCommand;
use Overseer\Project\Domain\Enum\ProjectMemberPermission;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Validator\AuthorizedProjectMember;
use Overseer\Project\Domain\Validator\ValidProject;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\Email;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;

class InviteMemberCommandValidator implements CommandValidator
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof InviteMemberCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . InviteMemberCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getProjectId(), 'Project does not exist', [
                new ValidUuid(),
                new ValidProject($this->projectReadModel)
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ProjectNotFoundException($validationContext->getErrorMessage());
        }

        $project = $this->projectReadModel->findById(ProjectId::fromString($command->getProjectId()));

        $validationContext = new ValidationContext([
            new Field($command->getIssuedBy(), 'Unauthorized request', [
                new AuthorizedProjectMember($project, ProjectMemberPermission::INVITE_NEW_MEMBERS()),
            ]),
            new Field($command->getEmail(), 'Invalid email', [
                new Email(),
            ]),
            new Field($command->getInvitationId(), 'Invalid invitation id', [
                new ValidUuid()
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}