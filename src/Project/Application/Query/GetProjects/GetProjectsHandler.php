<?php


namespace Overseer\Project\Application\Query\GetProjects;


use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Entity\ProjectMember;
use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\InvitationStatus;
use Overseer\Project\Domain\ValueObject\ProjectOwner;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\Bus\Query\QueryHandler;
use Overseer\Shared\Domain\ValueObject\PaginatedResult;

final class GetProjectsHandler implements QueryHandler
{
    private ProjectReadModel $projectReadModel;

    public function __construct(ProjectReadModel $projectReadModel)
    {
        $this->projectReadModel = $projectReadModel;
    }

    public function __invoke(GetProjects $query)
    {
        $projects = $this->projectReadModel->getProjects(
            $query->issuedBy(),
            $query->page(),
            $query->criteria(),
            $query->sort()
        );

        $projectsCount = $this->projectReadModel->count(
            $query->issuedBy(),
            $query->criteria()
        );

        $projectOwner = new ProjectOwner($query->issuedBy());
        $items = [];

        /** @var Project $project */
        foreach ($projects as $project) {
            if ($project->projectOwner()->equals($projectOwner)) {
                $items[] = $this->assembleProjectOwnerData($project);
                continue;
            }

            $items[] = $this->assembleProjectData($project, new Username($query->issuedBy()));
        }
        return new PaginatedResult(
            $items,
            $projectsCount,
            $query->page()
        );
    }

    private function assembleProjectOwnerData(Project $project)
    {
        $item = $this->projectToArray($project);

        $invitations = [];
        $projectMembers = [];

        /** @var ProjectMemberInvitation $invitation */
        foreach ($project->invitations() as $invitation) {
            $invitations[] = $this->invitationToArray($invitation);
        }

        /** @var ProjectMember $member */
        foreach ($project->members() as $member) {
            $projectMembers[] = $this->projectMemberToArray($member);
        }

        $item['invitations'] = $invitations;
        $item['members'] = $projectMembers;

        return $item;
    }

    private function assembleProjectData(Project $project, Username $username)
    {
        $item = $this->projectToArray($project);
        $status = new InvitationStatus(InvitationStatus::INVITED);

        $invitations = [];
        $projectMembers = [];

        /** @var ProjectMemberInvitation $invitation */
        foreach ($project->invitations() as $invitation) {
            if ($invitation->username()->equals($username) && $invitation->status()->equals($status)) {
                $invitations[] = $this->invitationToArray($invitation);
            }
        }

        /** @var ProjectMember $member */
        foreach ($project->members() as $member) {
            $projectMembers[] = $this->projectMemberToArray($member);
        }

        $item['invitations'] = $invitations;
        $item['members'] = $projectMembers;

        return $item;
    }

    private function projectToArray(Project $project)
    {
        return [
            'title' => $project->projectTitle()->value(),
            'description' => $project->description(),
            'slug' => $project->slug()->value(),
            'uuid' => $project->uuid(),
            'project_owner' => $project->projectOwner()->value(),
            'created_at' => [
                'timestamp' => $project->createdAt()->getTimestamp(),
                'timezone' => $project->createdAt()->getTimezone(),
            ],
        ];
    }

    private function invitationToArray(ProjectMemberInvitation $invitation)
    {
        return [
            'uuid' => $invitation->uuid()->value(),
            'username' => $invitation->username()->value(),
            'status' => $invitation->status()->value(),
            'invited_at' => [
                'timestamp' => $invitation->invitedAt()->getTimestamp(),
                'timezone' => $invitation->invitedAt()->getTimezone(),
            ],
            'responded_at' => !$invitation->respondedAt() ?? [
                    'timestamp' => $invitation->respondedAt()->getTimestamp(),
                    'timezone' => $invitation->respondedAt()->getTimezone()
                ]
        ];
    }

    private function projectMemberToArray(ProjectMember $member)
    {
        return [
            'uuid' => $member->uuid()->value(),
            'username' => $member->username()->value(),
            'joined_at' => [
                'timestamp' => $member->joinedAt()->getTimestamp(),
                'timezone' => $member->joinedAt()->getTimezone(),
            ]
        ];
    }
}