<?php


namespace Overseer\Project\Application\Command\DeclineInvitationCommand;


use Overseer\Project\Domain\Command\DeclineInvitationCommand;
use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Validator\InvitationHasStatus;
use Overseer\Project\Domain\Validator\InvitedMember;
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

class DeclineInvitationCommandValidator implements CommandValidator
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof DeclineInvitationCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . DeclineInvitationCommand::class . ' got ' . get_class($command));
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
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }

        $invitation = $project->getInvitations()->findInvitationWithId(ProjectMemberInvitationId::fromString($command->getInvitationId()));
        $validationContext = new ValidationContext([
            new Field($invitation, 'This invitation was already accepted or declined', [
                new InvitationHasStatus(InvitationStatus::INVITED())
            ]),
            new Field($command->getDeclinedBy(), 'This invitation does not belong to you', [
                new InvitedMember($invitation),
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}