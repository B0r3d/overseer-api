<?php


namespace Overseer\Project\Application\Command\CancelInvitationCommand;


use Overseer\Project\Domain\Command\CancelInvitationCommand;
use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\Enum\ProjectMemberPermission;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Validator\AuthorizedProjectMember;
use Overseer\Project\Domain\Validator\InvitationHasStatus;
use Overseer\Project\Domain\Validator\ValidInvitation;
use Overseer\Project\Domain\Validator\ValidProject;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Project\Domain\ValueObject\ProjectMemberInvitationId;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;

class CancelInvitationCommandValidator implements CommandValidator
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof CancelInvitationCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . CancelInvitationCommand::class . ' got ' . get_class($command));
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
            new Field($command->getInvitationId(), 'Invalid invitation', [
                new ValidUuid(),
                new ValidInvitation($project),
            ]),
            new Field($command->getIssuedBy(), 'Unauthorized request', [
                new AuthorizedProjectMember($project, ProjectMemberPermission::CANCEL_INVITATION())
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }

        $invitation = $project->getInvitations()->findInvitationWithId(ProjectMemberInvitationId::fromString($command->getInvitationId()));
        $validationContext = new ValidationContext([
            new Field($invitation, 'You can only cancel pending invites', [
                new InvitationHasStatus(InvitationStatus::INVITED())
            ]),
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }


}