<?php


namespace Overseer\Project\Domain\Enum;


use MyCLabs\Enum\Enum;

/**
 * @method static ProjectMemberPermission CREATE_API_KEY()
 * @method static ProjectMemberPermission REMOVE_API_KEY()
 * @method static ProjectMemberPermission INVITE_NEW_MEMBERS()
 * @method static ProjectMemberPermission REMOVE_PROJECT_MEMBERS()
 * @method static ProjectMemberPermission CANCEL_INVITATION()
 */
class ProjectMemberPermission extends Enum
{
    private const CREATE_API_KEY = 'create_api_key';
    private const REMOVE_API_KEY = 'remove_api_key';
    private const INVITE_NEW_MEMBERS = 'invite_new_members';
    private const REMOVE_PROJECT_MEMBERS = 'remove_project_members';
    private const CANCEL_INVITATION = 'cancel_invitation';
}