<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Dto\ProjectErrorResource;
use Overseer\Project\Domain\Query\GetProjectErrorQuery;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetProjectErrorAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $query = new GetProjectErrorQuery(
            $request->get('_project_id'),
            $request->get('_error_id'),
            $subject->getUsername()
        );

        /** @var SingleObjectResult $result */
        $result = $this->ask($query);

        return $this->respondWithOk(new ProjectErrorResource($result->getData()));
    }
}