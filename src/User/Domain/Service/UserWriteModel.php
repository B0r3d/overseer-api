<?php


namespace Overseer\User\Domain\Service;


use Overseer\User\Domain\Entity\User;

interface UserWriteModel
{
    function save(User $user): void;
    function delete(User $user): void;
}