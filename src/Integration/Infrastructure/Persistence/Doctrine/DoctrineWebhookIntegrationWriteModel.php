<?php


namespace Overseer\Integration\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Integration\Domain\Entity\WebhookIntegration;
use Overseer\Integration\Domain\Service\WebhookIntegrationWriteModel;

class DoctrineWebhookIntegrationWriteModel implements WebhookIntegrationWriteModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(WebhookIntegration $integration): void
    {
        $this->em->persist($integration);
        $this->em->flush();
    }

    public function delete(WebhookIntegration $integration): void
    {
        $this->em->remove($integration);
        $this->em->flush();
    }
}