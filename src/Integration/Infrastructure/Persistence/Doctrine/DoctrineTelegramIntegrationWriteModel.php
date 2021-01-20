<?php


namespace Overseer\Integration\Infrastructure\Persistence\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Overseer\Integration\Domain\Entity\TelegramIntegration;
use Overseer\Integration\Domain\Service\TelegramIntegrationWriteModel;

class DoctrineTelegramIntegrationWriteModel implements TelegramIntegrationWriteModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(TelegramIntegration $integration): void
    {
        $this->em->persist($integration);
        $this->em->flush();
    }

    public function delete(TelegramIntegration $integration): void
    {
        $this->em->remove($integration);
        $this->em->flush();
    }
}