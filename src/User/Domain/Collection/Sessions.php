<?php


namespace Overseer\User\Domain\Collection;


use Overseer\User\Domain\Entity\Session;
use Overseer\User\Domain\ValueObject\JsonWebToken;

class Sessions extends \ArrayObject
{
    public function __construct(array $input = [])
    {
        parent::__construct($input);
    }

    public function findByRefreshToken(JsonWebToken $token): ?Session
    {
        /** @var Session $session */
        foreach($this as $session) {
            if ($session->getRefreshToken() === $token->getToken()) {
                return $session;
            }
        }

        return null;
    }

    public function save(Session $updatedSession)
    {
        /** @var Session $session */
        foreach($this as $index => $session) {
            if ($session === $updatedSession) {
                $this->offsetSet($index, $updatedSession);
                break;
            }
        }
    }

    public function terminateSessions(array $sessionsToSkip = [])
    {
        /** @var Session $session */
        foreach ($this as $session) {
            if (!$this->isSessionToSkip($session, $sessionsToSkip)) {
                $session->invalidate();
            }
        }
    }

    private function isSessionToSkip(Session $session, $sessionsToSkip)
    {
        foreach ($sessionsToSkip as $sessionId) {
            if ($session->getId() === $sessionId) {
                return true;
            }
        }

        return false;
    }
}