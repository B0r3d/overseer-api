<?php


namespace Overseer\Integration\Infrastructure\CLI;


use Overseer\Integration\Domain\Entity\WebhookIntegrationMessage;
use Overseer\Integration\Domain\Service\IntegrationMessageSender;
use Overseer\Integration\Domain\Service\WebhookIntegrationReadModel;
use Overseer\Integration\Domain\Service\WebhookIntegrationWriteModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;

class ProcessWebhookIntegrationMessagesCommand extends Command
{
    protected static $defaultName = 'overseer:integration:process-webhook-integration-messages';
    private WebhookIntegrationReadModel $webhookIntegrationReadModel;
    private WebhookIntegrationWriteModel $webhookIntegrationWriteModel;
    private ProcessWebhookIntegrationMessagesCommandInvoker $invoker;
    private IntegrationMessageSender $sender;

    public function __construct(WebhookIntegrationReadModel $webhookIntegrationReadModel, WebhookIntegrationWriteModel $webhookIntegrationWriteModel, ProcessWebhookIntegrationMessagesCommandInvoker $invoker, IntegrationMessageSender $sender, string $name = null)
    {
        $this->webhookIntegrationReadModel = $webhookIntegrationReadModel;
        $this->webhookIntegrationWriteModel = $webhookIntegrationWriteModel;
        $this->invoker = $invoker;
        $this->sender = $sender;

        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);

        $lock = $factory->createLock('webhook-integration-messages-processing');

        if ($lock->acquire()) {
            $integrationMessages = $this->webhookIntegrationReadModel->findUnprocessedMessages();

            /** @var WebhookIntegrationMessage $message */
            foreach ($integrationMessages as $message) {
                $this->sender->resendMessage($message);
                $this->webhookIntegrationWriteModel->save($message->getIntegration());
            }

            $lock->release();
            if ($this->webhookIntegrationReadModel->findUnprocessedMessagesCount() > 0) {
                $this->invoker->invoke();
                return;
            }
        }

        if ($lock->acquire()) {
            $integrationMessages = $this->webhookIntegrationReadModel->findFailedMessages();

            /** @var WebhookIntegrationMessage $message */
            foreach ($integrationMessages as $message) {
                $this->sender->resendMessage($message);
                $this->webhookIntegrationWriteModel->save($message->getIntegration());
            }

            $lock->release();
            if ($this->webhookIntegrationReadModel->findFailedMessagesCount() > 0) {
                $this->invoker->invoke();
            }
        }
    }
}