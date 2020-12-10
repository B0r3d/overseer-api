<?php


namespace Overseer\User\Domain\ValueObject;


use Overseer\Shared\Domain\ValueObject\ExpiryDate;

class PasswordResetToken
{
    private string $id;
    private PasswordResetTokenId $_id;
    private ExpiryDate $expiryDate;

    public function __construct(PasswordResetTokenId $_id, ExpiryDate $expiryDate)
    {
        $this->id = $_id->value();
        $this->_id = $_id;
        $this->expiryDate = $expiryDate;
    }

    public function getId(): string
    {
        if (isset($this->_id)) {
            return $this->_id;
        }

        $this->_id = PasswordResetTokenId::fromString($this->id);
        return $this->_id;
    }

    public function getExpiryDate(): ExpiryDate
    {
        return $this->expiryDate;
    }
}