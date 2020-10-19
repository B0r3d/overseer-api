<?php


namespace Overseer\Project\Domain\Service;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\ValueObject\Slug;

interface ProjectReadModel
{
    public function findBySlug(Slug $slug): ?Project;
}