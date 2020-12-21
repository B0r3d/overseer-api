<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Dto\InviteUserResource;
use Overseer\User\Domain\Entity\User;
use Overseer\User\Domain\Query\GetUsersQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetUsersAction extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $paramFetcher = $this->getParamFetcher($request);

        /** @var PaginatedQueryResult $result */
        $result = $this->ask(new GetUsersQuery(
            $paramFetcher->getQueryParameter('page', 1),
            [
                'search' => $paramFetcher->getQueryParameter('search', ''),
            ]
        ));

        $resources = [];

        /** @var User $item */
        foreach($result->getItems() as $item) {
            $resources[] = new InviteUserResource($item);
        }

        return $this->respondWithOk(new PaginatedQueryResult(
            $resources,
            $result->getCount(),
            $result->getPage()
        ));
    }
}