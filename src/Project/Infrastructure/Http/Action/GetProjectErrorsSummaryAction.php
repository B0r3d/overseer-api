<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Query\GetProjectErrorsSummaryQuery;
use Overseer\Shared\Domain\ValueObject\SingleObjectResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetProjectErrorsSummaryAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();
        $paramFetcher = $this->getParamFetcher($request);

        $query = new GetProjectErrorsSummaryQuery(
            $request->get('_project_id'),
            $subject->getUsername(),
            [
                'search' => $paramFetcher->getQueryParameter('search', ''),
                'date_from' => $paramFetcher->getQueryParameter('date_from', ''),
                'date_to' => $paramFetcher->getQueryParameter('date_to', ''),
            ]
        );

        /** @var SingleObjectResult $result */
        $result = $this->ask($query);

        return $this->respondWithOk($result->getData());
    }
}