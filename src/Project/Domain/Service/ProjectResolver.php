<?php


namespace Overseer\Project\Domain\Service;


use Overseer\Project\Domain\Entity\Project;

interface ProjectResolver
{
    public function resolve(string $apiKey): ?Project;
}