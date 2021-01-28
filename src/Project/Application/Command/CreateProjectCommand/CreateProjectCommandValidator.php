<?php


namespace Overseer\Project\Application\Command\CreateProjectCommand;


use Overseer\Project\Domain\Command\CreateProjectCommand;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Validator\AvailableSlug;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\MinLength;
use Overseer\Shared\Domain\Validator\Specification\ValidSlug;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;

class CreateProjectCommandValidator implements CommandValidator
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof CreateProjectCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . CreateProjectCommand::class . ' got ' . get_class($command));
        }

        $validationContext = new ValidationContext([
            new Field($command->getProjectId(), 'Invalid project id', [
                new ValidUuid(),
            ]),
            new Field($command->getProjectTitle(), 'Invalid project title', [
                new MinLength(4),
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}