<?php


namespace Overseer\User\Infrastructure\Http\Action;


use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Overseer\User\Domain\Service\UserReadModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckPasswordResetTokenAction extends AbstractAction
{
    private UserReadModel $userReadModel;

    public function __construct(UserReadModel $userReadModel)
    {
        $this->userReadModel = $userReadModel;
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->userReadModel->findUserByPasswordResetToken($request->get('_password_reset_token'));

        if (!$user || $user->getPasswordResetToken()->getExpiryDate()->isExpired()) {
            throw new ValidationException('Invalid or expired password reset token');
        }

        return $this->respondWithOk();
    }
}