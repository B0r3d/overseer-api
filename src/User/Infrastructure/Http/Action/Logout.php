<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\RefreshTokenId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Logout
{
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;

    public function __construct(UserReadModel $userReadModel, UserWriteModel $userWriteModel)
    {
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
    }

    public function __invoke(Request $request): Response
    {
        $refreshToken = $request->cookies->get('refresh_token');

        if (!$refreshToken) {
            return new JsonResponse([
                'ok' => true,
            ]);
        }

        $refreshToken = json_decode(\Firebase\JWT\JWT::urlsafeB64Decode($refreshToken), true);
        $user = $this->userReadModel->findOneByLogin($refreshToken['sub']);

        $refreshTokenId = RefreshTokenId::fromString($refreshToken['token_id']);
        $user->removeRefreshToken($refreshTokenId);
        $this->userWriteModel->save($user);
        setcookie('refresh_token', '', time()-3600);

        return new JsonResponse([
            'ok' => true,
        ]);
    }
}