<?php


namespace Overseer\User\Infrastructure\Security;


use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Infrastructure\Http\ErrorResponse;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\ValueObject\JsonWebToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

final class BearerAuthenticator extends AbstractGuardAuthenticator
{
    private JWT $jwt;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw new UnauthorizedException('No JWT provided.');
    }

    public function supports(Request $request)
    {
        return $request->headers->get('Authorization');
    }

    public function getCredentials(Request $request)
    {
        return $request->headers->get('Authorization');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $jwt = str_replace('Bearer ', '', $credentials);
            $credentials = $this->jwt->decodeToken($jwt);
            $user = $userProvider->loadUserByUsername($credentials->getSubject());
            return Subject::fromUser($user);
        } catch(\Throwable $t) {
            dump($t);
            die;
            return null;
        }

    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        try {
            $jwt = str_replace('Bearer ', '', $credentials);
            $token = $this->jwt->decodeToken($jwt);
            return $this->jwt->verify($token);
        } catch(\Throwable $t) {
            return false;
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new ErrorResponse(401, 'Your token is invalid or expired.', 0);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // Don't do anything
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}