<?php


namespace Overseer\Project\Application\Command\CreateErrorCommand;


use Overseer\Project\Domain\Command\CreateErrorCommand;
use Overseer\Project\Domain\Exception\ProjectNotFoundException;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\Validator\ValidProject;
use Overseer\Project\Domain\Validator\ValidStacktrace;
use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandValidator;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Domain\Validator\Field;
use Overseer\Shared\Domain\Validator\Specification\GreaterThan;
use Overseer\Shared\Domain\Validator\Specification\NotBlank;
use Overseer\Shared\Domain\Validator\Specification\ValidTimestamp;
use Overseer\Shared\Domain\Validator\Specification\ValidUuid;
use Overseer\Shared\Domain\Validator\ValidationContext;

class CreateErrorCommandValidator implements CommandValidator
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function validate(Command $command)
    {
        if (!$command instanceof CreateErrorCommand) {
            throw new \InvalidArgumentException('Command must be an instance of ' . CreateErrorCommand::class . ' got ' . get_class($command));
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

        $validationContext = new ValidationContext([
            new Field($command->getErrorId(), 'Invalid UUID', [
                new ValidUuid(),
            ]),
            new Field($command->getClass(), 'Class cannot be empty', [
                new NotBlank(),
            ]),
            new Field($command->getLine(), 'Invalid line', [
                new GreaterThan(0),
            ]),
            new Field($command->getFile(), 'Invalid file name', [
                new NotBlank(),
            ]),
            new Field($command->getOccurredAt(), 'Invalid timestamp', [
                new ValidTimestamp(),
            ]),
            new Field($command->getStacktrace(), 'Invalid stacktrace', [
                new ValidStacktrace()
            ])
        ]);

        if (!$validationContext->isValid()) {
            throw new ValidationException($validationContext->getErrorMessage());
        }
    }
}