<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Dto\ProjectErrorsListResource;
use Overseer\Project\Domain\Entity\Error;
use Overseer\Project\Domain\Query\GetProjectErrorsQuery;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetProjectErrorsAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $paramFetcher = $this->getParamFetcher($request);

        $query = new GetProjectErrorsQuery(
            $request->get('_project_id'),
            $subject->getUsername(),
            $paramFetcher->getQueryParameter('page', 1),
            [
                'search' => $paramFetcher->getQueryParameter('search', ''),
                'date_from' => $paramFetcher->getQueryParameter('date_from', ''),
                'date_to' => $paramFetcher->getQueryParameter('date_to', ''),
            ],
            [
                $paramFetcher->getQueryParameter('sort_by', '')
            ]
        );

        /** @var PaginatedQueryResult $result */
        $result = $this->ask($query);

        $items = $result->getItems();
        $resources = [];

        /** @var Error $error */
        foreach ($items as $error) {
            $resources[] = new ProjectErrorsListResource($error);
        }

        $result = new PaginatedQueryResult(
            $resources,
            $result->getCount(),
            $result->getPage()
        );

        return $this->respondWithOk($result);
    }
}