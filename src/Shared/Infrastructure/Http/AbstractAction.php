<?php


namespace Overseer\Shared\Infrastructure\Http;


use Overseer\Shared\Domain\Bus\Command\Command;
use Overseer\Shared\Domain\Bus\Command\CommandBus;
use Overseer\Shared\Domain\Bus\Query\Query;
use Overseer\Shared\Domain\Bus\Query\QueryBus;
use Overseer\Shared\Domain\Bus\Query\Result;
use Overseer\User\Infrastructure\Security\Subject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAction extends AbstractController
{
    private CommandBus $commandBus;
    private QueryBus $queryBus;
    private ParamFetcherFactory $paramFetcherFactory;

    public function setCommonDependencies(CommandBus $commandBus, QueryBus $queryBus, ParamFetcherFactory $paramFetcherFactory)
    {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->paramFetcherFactory = $paramFetcherFactory;
    }

    abstract public function __invoke(Request $request): Response;

    protected function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }

    protected function ask(Query $query): Result
    {
        return $this->queryBus->ask($query);
    }

    protected function getParamFetcher(Request $request): ParamFetcher
    {
        return $this->paramFetcherFactory->createFetcher($request);
    }

    protected function respondWithOk($payload = [], array $headers = []): Response
    {
        return $this->respond($payload, Response::HTTP_OK, $headers);
    }

    protected function respondWithCreated($payload = [], array $headers = []): Response
    {
        return $this->respond($payload, Response::HTTP_CREATED, $headers);
    }

    protected function respondWithAccepted($payload = [], array $headers = []): Response
    {
        return $this->respond($payload, Response::HTTP_ACCEPTED, $headers);
    }

    private function respond($payload, int $statusCode, array $headers = []): Response
    {
        return new SuccessResponse($payload, $statusCode, $headers);
    }

    /**
     * @return object|Subject
     */
    protected function getUser()
    {
        return parent::getUser();
    }
}