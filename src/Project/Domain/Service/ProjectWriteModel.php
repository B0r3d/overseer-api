<?php


namespace Overseer\Project\Domain\Service;


use Overseer\Project\Domain\Entity\Project;

interface ProjectWriteModel
{
    public function save(Project $project): void;
    public function delete(Project $project): void;
}