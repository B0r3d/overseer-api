<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use JMS\Serializer\SerializerInterface;
use Overseer\Project\Application\Query\GetProjects\GetProjects as GetProjectsAlias;
use Overseer\Shared\Domain\Bus\Query\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetProjects extends AbstractController
{
    private QueryBus $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function __invoke(Request $request): Response
    {
        $query = new GetProjectsAlias(
            $this->getUser()->getUsername(),
            $request->query->get('page', 1),
            $request->query->get('criteria', []),
            $request->query->get('sort', [])
        );

        $response = $this->queryBus->ask($query);

        return $this->json([
            'ok' => true,
            'payload' => $response,
        ]);
    }
}