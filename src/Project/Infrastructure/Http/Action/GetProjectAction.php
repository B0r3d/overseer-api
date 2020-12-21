<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Dto\ApiKeyResource;
use Overseer\Project\Domain\Dto\ProjectMemberInvitationResource;
use Overseer\Project\Domain\Dto\ProjectMemberResource;
use Overseer\Project\Domain\Dto\ProjectResource;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Entity\ProjectMemberInvitation;
use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\Query\GetProjectQuery;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetProjectAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $query = new GetProjectQuery(
            $request->get('_project_id'),
            $subject->getUsername()
        );

        /** @var SingleObjectResult $result */
        $result = $this->ask($query);

        /** @var Project $project */
        $project = $result->getData();

        $username = new Username($query->getIssuedBy());
        if ($project->isProjectOwner($username)) {
            $result = $this->createProjectOwnerResource($project);
        } else {
            $result = $this->createProjectMemberResource($project, $username);
        }

        return $this->respondWithOk($result);
    }

    private function createProjectOwnerResource(Project $project)
    {
        $projectResource = new ProjectResource($project);
        $invitations = $project->getInvitations();
        foreach($invitations as $invitation) {
            $projectResource->addInvitation(new ProjectMemberInvitationResource($invitation));
        }

        $members = $project->getMembers();
        foreach($members as $member) {
            $projectResource->addProjectMember(new ProjectMemberResource($member));
        }

        $apiKeys = $project->getApiKeys();
        foreach($apiKeys as $apiKey) {
            $projectResource->addApiKey(new ApiKeyResource($apiKey));
        }

        return $projectResource;
    }

    private function createProjectMemberResource(Project $project, Username $username)
    {
        $projectResource = new ProjectResource($project);
        $invitations = $project->getInvitations();

        /** @var ProjectMemberInvitation $invitation */
        foreach($invitations as $invitation) {
            if ($invitation->getUsername()->equals($username) && $invitation->getStatus()->equals(InvitationStatus::INVITED())) {
                $projectResource->addInvitation(new ProjectMemberInvitationResource($invitation));
            }
        }

        $members = $project->getMembers();
        foreach($members as $member) {
            $projectResource->addProjectMember(new ProjectMemberResource($member));
        }

        $apiKeys = $project->getApiKeys();
        foreach($apiKeys as $apiKey) {
            $projectResource->addApiKey(new ApiKeyResource($apiKey));
        }

        return $projectResource;
    }
}