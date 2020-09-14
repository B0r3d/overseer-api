<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\User\Domain\Exception\InvalidRefreshTokenException;
use Overseer\User\Domain\Exception\RefreshTokenNotFoundException;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\Service\UserReadModel;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\RefreshTokenId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RefreshToken
{
    private UserReadModel $userReadModel;
    private UserWriteModel $userWriteModel;
    private JWT $jwt;

    public function __construct(UserReadModel $userReadModel, UserWriteModel $userWriteModel, JWT $jwt)
    {
        $this->userReadModel = $userReadModel;
        $this->userWriteModel = $userWriteModel;
        $this->jwt = $jwt;
    }

    public function __invoke(Request $request): Response
    {
        $refreshToken = $request->cookies->get('refresh_token');

        if (!$refreshToken) {
            throw new RefreshTokenNotFoundException();
        }

        $refreshToken = json_decode(\Firebase\JWT\JWT::urlsafeB64Decode($refreshToken), true);
        $user = $this->userReadModel->findOneByLogin($refreshToken['sub']);

        $refreshTokenId = RefreshTokenId::fromString($refreshToken['token_id']);
        $isValid = $user->isValid($refreshTokenId);
        $user->removeRefreshToken($refreshTokenId);
        if (!$isValid) {
            $this->userWriteModel->save($user);
            throw new InvalidRefreshTokenException();
        }

        $accessToken = $this->jwt->issueToken($user);
        $refreshToken = $this->jwt->createRefreshToken($user);

        $user->addRefreshToken($refreshToken);
        $this->userWriteModel->save($user);

        $base64RefreshToken = \Firebase\JWT\JWT::urlsafeB64Encode(json_encode([
            'sub' => $user->username()->value(),
            'token_id' => $refreshToken->uuid()->value(),
        ]));

        setcookie(
            'refresh_token',
            $base64RefreshToken,
            $refreshToken->expiryDate()->value()->getTimestamp(),
            '',
            '',
            true,
            true
        );

        return new JsonResponse([
            'token' => $accessToken->token(),
        ]);
    }
}