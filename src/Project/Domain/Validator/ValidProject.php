<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\ProjectId;
use Overseer\Shared\Domain\Validator\Specification;

class ValidProject implements Specification
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function isSatisfiedBy($value): bool
    {
        $project = $this->projectReadModel->findByUuid(ProjectId::fromString($value));
        return $project !== null;
    }
}