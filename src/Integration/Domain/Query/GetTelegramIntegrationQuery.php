<?php


namespace Overseer\Integration\Domain\Query;


use Overseer\Shared\Domain\ValueObject\SingleObjectQuery;

class GetTelegramIntegrationQuery extends SingleObjectQuery
{
    private string $issuedBy;

    public function __construct(string $issuedBy, string $id)
    {
        $this->issuedBy = $issuedBy;

        parent::__construct($id);
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }
}