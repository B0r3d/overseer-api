<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\User\Domain\Service\UserReadModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class Me extends AbstractController
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function __invoke(Request $request)
    {
        $user = $this->userReadModel->findOneByLogin($this->getUser()->getUsername());

        return new JsonResponse([
            'ok' => true,
            'payload' => [
                'user' => [
                    'uuid' => $user->uuid()->value(),
                    'username' => $user->username()->value(),
                    'email' => $user->email()->value(),
                ]
            ]
        ]);
    }
}