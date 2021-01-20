<?php


namespace Overseer\User\Infrastructure\Http\Action;


use JMS\Serializer\SerializerInterface;
use Overseer\Shared\Domain\ValueObject\Cookie;
use Overseer\Shared\Domain\ValueObject\Uuid;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\Shared\Infrastructure\Http\CookieManager;
use Overseer\User\Domain\Command\StartUserSessionCommand;
use Overseer\User\Domain\Dto\AuthenticateRequest;
use Overseer\User\Domain\Service\Authenticator;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\Service\UserWriteModel;
use Overseer\User\Domain\ValueObject\AuthUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class AuthenticateUserAction extends AbstractAction
{
    private Authenticator $authenticator;
    private JWT $jwt;
    private CookieManager $cookieManager;

    public function __construct(Authenticator $authenticator, JWT $jwt, CookieManager $cookieManager)
    {
        $this->authenticator = $authenticator;
        $this->jwt = $jwt;
        $this->cookieManager = $cookieManager;
    }

    public function __invoke(Request $request): Response
    {
        $paramFetcher = $this->getParamFetcher($request);
        $authUser = new AuthUser(
            $paramFetcher->getDataParameter('login', ''),
            $paramFetcher->getDataParameter('password', ''),
        );

        $authenticatedUser = $this->authenticator->authenticate($authUser);
        $tokenPair = $this->jwt->createTokens($authenticatedUser);

        $command = new StartUserSessionCommand(
            $tokenPair->getRefreshToken()->getToken(),
            Uuid::random()->value(),
            $authenticatedUser->getId(),
            $tokenPair->getRefreshToken()->getExpiryTime(),
            $tokenPair->getRefreshToken()->getIssuedAt()
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