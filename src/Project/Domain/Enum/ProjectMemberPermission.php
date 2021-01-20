<?php


namespace Overseer\Project\Domain\Enum;


use MyCLabs\Enum\Enum;

/**
 * @method static ProjectMemberPermission CREATE_API_KEY()
 * @method static ProjectMemberPermission REMOVE_API_KEY()
 * @method static ProjectMemberPermission INVITE_NEW_MEMBERS()
 * @method static ProjectMemberPermission REMOVE_PROJECT_MEMBERS()
 * @method static ProjectMemberPermission CANCEL_INVITATION()
 * @method static ProjectMemberPermission MANAGE_TELEGRAM_INTEGRATION()
 * @method static ProjectMemberPermission MANAGE_WEBHOOK_INTEGRATION()
 * @method static ProjectMemberPermission DELETE_PROJECT()
 * @method static ProjectMemberPermission UPDATE_PROJECT()
 */
class ProjectMemberPermission extends Enum
{
    private const CREATE_API_KEY = 'create_api_key';
    private const REMOVE_API_KEY = 'remove_api_key';
    private const INVITE_NEW_MEMBERS = 'invite_new_members';
    private const REMOVE_PROJECT_MEMBERS = 'remove_project_members';
    private const CANCEL_INVITATION = 'cancel_invitation';
    private const MANAGE_TELEGRAM_INTEGRATION = 'manage_telegram_integration';
    private const MANAGE_WEBHOOK_INTEGRATION = 'manage_webhook_integration';
    private const DELETE_PROJECT = 'delete_project';
    private const UPDATE_PROJECT = 'update_project';
}