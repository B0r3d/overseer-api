<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\Shared\Infrastructure\Http\CookieManager;
use Overseer\User\Domain\Command\InvalidateRefreshTokenCommand;
use Overseer\User\Domain\Service\JWT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LogoutAction extends AbstractAction
{
    private CookieManager $cookieManager;

    public function __construct(CookieManager $cookieManager)
    {
        $this->cookieManager = $cookieManager;
    }


    public function __invoke(Request $request): Response
    {
        $refreshToken = $this->cookieManager->getCookie(JWT::REFRESH_TOKEN_COOKIE);

        if (!$refreshToken) {
            return $this->respondWithAccepted();
        }

        $this->dispatch(new InvalidateRefreshTokenCommand(
            $refreshToken
        ));

        $this->cookieManager->removeCookie(JWT::REFRESH_TOKEN_COOKIE);

        return $this->respondWithAccepted();
    }
}