<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Dto\ProjectListItemResource;
use Overseer\Project\Domain\Entity\Project;
use Overseer\Project\Domain\Enum\InvitationStatus;
use Overseer\Project\Domain\Query\GetProjectsQuery;
use Overseer\Project\Domain\ValueObject\Username;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetProjectsAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $paramFetcher = $this->getParamFetcher($request);

        $query = new GetProjectsQuery(
            $this->getUser()->getUsername(),
            $paramFetcher->getQueryParameter('page', 1),
            [
                'search' => $paramFetcher->getQueryParameter('search', ''),
            ],
            $paramFetcher->getQueryParameter('sort', [])
        );

        /** @var PaginatedQueryResult $result */
        $result = $this->ask($query);

        $items = $result->getItems();
        $resources = [];

        /** @var Project $project */
        foreach ($items as $project) {
            $invitations = $project->getInvitations();
            $invitation = $invitations->findInvitationWithUsername(new Username($query->getIssuedBy()));

            if ($invitation && $invitation->getStatus()->equals(InvitationStatus::INVITED())) {
                $resources[] = new ProjectListItemResource($project, $invitation);
            } else {
                $resources[] = new ProjectListItemResource($project);
            }
        }

        $result = new PaginatedQueryResult(
            $resources,
            $result->getCount(),
            $result->getPage()
        );

        return $this->respondWithOk($result);
    }
}