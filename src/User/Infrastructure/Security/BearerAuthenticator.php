<?php


namespace Overseer\User\Infrastructure\Security;


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
        return new JsonResponse([
            'ok' => false,
            'error_message' => 'No JWT provided.'
        ], Response::HTTP_UNAUTHORIZED);
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
            $user = $userProvider->loadUserByUsername($credentials['sub']);
            return Subject::fromUser($user);
        } catch(\Throwable $t) {
            return null;
        }

    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $jwt = new JsonWebToken(str_replace('Bearer ', '', $credentials));
        return $this->jwt->verify($jwt);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // TODO dispatch event to log the auth failure.
        return new JsonResponse([
            'ok' => false,
            'error_message' => 'Your token is invalid or expired.',
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // TODO log the success.
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}