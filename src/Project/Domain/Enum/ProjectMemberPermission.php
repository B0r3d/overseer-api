<?php


namespace Overseer\Project\Domain\Enum;


use MyCLabs\Enum\Enum;

/**
 * @method static ProjectMemberPermission CREATE_API_KEY()
 * @method static ProjectMemberPermission REMOVE_API_KEY()
 */
class ProjectMemberPermission extends Enum
{
    private const CREATE_API_KEY = 'create_api_key';
    private const REMOVE_API_KEY = 'remove_api_key';
}