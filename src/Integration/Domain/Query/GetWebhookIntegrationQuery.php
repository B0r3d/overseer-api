<?php


namespace Overseer\Integration\Domain\Query;


use Overseer\Shared\Domain\ValueObject\SingleObjectQuery;

final class GetWebhookIntegrationQuery extends SingleObjectQuery
{
    private string $issuedBy;

    public function __construct(string $id, string $issuedBy)
    {
        $this->issuedBy = $issuedBy;

        parent::__construct($id);
    }

    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }
}