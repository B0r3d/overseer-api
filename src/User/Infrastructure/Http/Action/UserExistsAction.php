<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Service\UserReadModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserExistsAction extends AbstractAction
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function __invoke(Request $request): Response
    {
        $paramFetcher = $this->getParamFetcher($request);
        $username = $paramFetcher->getQueryParameter('username');
        $email = $paramFetcher->getQueryParameter('email');

        if (!$username && !$email) {
            throw new ValidationException('No username or email provided. Add one or both to query parameters.');
        }

        $user = null;

        if ($username) {
            $user = $this->userReadModel->findOneByLogin($username);
        }

        if ($email && !$user) {
            $user = $this->userReadModel->findOneByLogin($email);
        }

        if (!$user) {
            throw new NotFoundException();
        }

        return $this->respondWithOk();
    }
}