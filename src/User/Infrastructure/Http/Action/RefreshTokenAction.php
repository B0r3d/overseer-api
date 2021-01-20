<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Domain\ValueObject\Cookie;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\Shared\Infrastructure\Http\CookieManager;
use Overseer\User\Domain\Command\UpdateUserSessionCommand;
use Overseer\User\Domain\Exception\InvalidRefreshTokenException;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\Service\UserReadModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RefreshTokenAction extends AbstractAction
{
    private JWT $jwt;
    private UserReadModel $userReadModel;
    private CookieManager $cookieManager;

    public function __construct(JWT $jwt, UserReadModel $userReadModel, CookieManager $cookieManager)
    {
        $this->jwt = $jwt;
        $this->userReadModel = $userReadModel;
        $this->cookieManager = $cookieManager;
    }

    public function __invoke(Request $request): Response
    {
        $refreshTokenString = $request->cookies->get(JWT::REFRESH_TOKEN_COOKIE);
        $refreshToken = $this->jwt->decodeToken($refreshTokenString);

        if (!$refreshToken) {
            throw new InvalidRefreshTokenException();
        }

        $user = $this->userReadModel->findOneByLogin($refreshToken->getSubject());
        $tokenPair = $this->jwt->createTokens($user);

        $command = new UpdateUserSessionCommand(
            $user->getUsername()->getValue(),
            $refreshToken->getToken(),
            $tokenPair->getRefreshToken()->getToken()
        );

        $this->dispatch($command);

        $this->cookieManager->addCookie(new Cookie(
            JWT::REFRESH_TOKEN_COOKIE,
            $tokenPair->getRefreshToken()->getToken(),
            $tokenPair->getRefreshToken()->getExpiryTime(),
            true
        ));

        return $this->respondWithOk($tokenPair);
    }
}