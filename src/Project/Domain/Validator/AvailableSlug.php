<?php


namespace Overseer\Project\Domain\Validator;


use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\Slug;
use Overseer\Shared\Domain\Validator\Specification;

class AvailableSlug implements Specification
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function isSatisfiedBy($value): bool
    {
        $slug = new Slug($value);
        $project = $this->projectReadModel->findBySlug($slug);
        return $project === null;
    }
}