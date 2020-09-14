<?php


namespace Overseer\User\Infrastructure\Http\Action;


use JMS\Serializer\SerializerInterface;
use Overseer\User\Domain\Dto\AuthenticateRequest;
use Overseer\User\Domain\Service\Authenticator;
use Overseer\User\Domain\Service\JWT;
use Overseer\User\Domain\Service\UserWriteModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class Authenticate
{
    private SerializerInterface $serializer;
    private Authenticator $authenticator;
    private JWT $jwt;
    private UserWriteModel $userWriteModel;

    public function __construct(
        SerializerInterface $serializer,
        Authenticator $authenticator,
        JWT $jwt,
        UserWriteModel $userWriteModel
    ) {
        $this->serializer = $serializer;
        $this->authenticator = $authenticator;
        $this->jwt = $jwt;
        $this->userWriteModel = $userWriteModel;
    }

    public function __invoke(Request $request): Response
    {
        /** @var AuthenticateRequest $authRequest */
        $authRequest = $this->serializer->deserialize($request->getContent(), AuthenticateRequest::class, 'json');

        if (!$authRequest->isValid()) {
            throw new BadRequestHttpException();
        }

        // This will throw exception if something is wrong
        $user = $this->authenticator->authenticate($authRequest);

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