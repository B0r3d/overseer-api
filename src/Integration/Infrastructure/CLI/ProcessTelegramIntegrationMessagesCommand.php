<?php


namespace Overseer\Integration\Infrastructure\CLI;


use Overseer\Integration\Domain\Entity\TelegramIntegrationMessage;
use Overseer\Integration\Domain\Service\IntegrationMessageSender;
use Overseer\Integration\Domain\Service\TelegramIntegrationReadModel;
use Overseer\Integration\Domain\Service\TelegramIntegrationWriteModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;

class ProcessTelegramIntegrationMessagesCommand extends Command
{
    protected static $defaultName = 'overseer:integration:process-telegram-integration-messages';
    private TelegramIntegrationReadModel $telegramIntegrationReadModel;
    private TelegramIntegrationWriteModel $telegramIntegrationWriteModel;
    private ProcessTelegramIntegrationMessagesCommandInvoker $invoker;
    private IntegrationMessageSender $sender;

    /**
     * ProcessTelegramIntegrationMessagesCommand constructor.
     * @param TelegramIntegrationReadModel $telegramIntegrationReadModel
     * @param TelegramIntegrationWriteModel $telegramIntegrationWriteModel
     * @param ProcessTelegramIntegrationMessagesCommandInvoker $invoker
     * @param IntegrationMessageSender $sender
     */
    public function __construct(TelegramIntegrationReadModel $telegramIntegrationReadModel, TelegramIntegrationWriteModel $telegramIntegrationWriteModel, ProcessTelegramIntegrationMessagesCommandInvoker $invoker, IntegrationMessageSender $sender, string $name = null)
    {
        $this->telegramIntegrationReadModel = $telegramIntegrationReadModel;
        $this->telegramIntegrationWriteModel = $telegramIntegrationWriteModel;
        $this->invoker = $invoker;
        $this->sender = $sender;

        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);

        $lock = $factory->createLock('telegram-integration-messages-processing');

        if ($lock->acquire()) {
            $integrationMessages = $this->telegramIntegrationReadModel->findUnprocessedMessages();

            /** @var TelegramIntegrationMessage $message */
            foreach ($integrationMessages as $message) {
                $this->sender->resendMessage($message);
                $this->telegramIntegrationWriteModel->save($message->getIntegration());
            }

            $lock->release();
            if ($this->telegramIntegrationReadModel->findUnprocessedMessagesCount() > 0) {
                $this->invoker->invoke();
                return;
            }
        }

        if ($lock->acquire()) {
            $integrationMessages = $this->telegramIntegrationReadModel->findFailedMessages();

            /** @var TelegramIntegrationMessage $message */
            foreach ($integrationMessages as $message) {
                $this->sender->resendMessage($message);
                $this->telegramIntegrationWriteModel->save($message->getIntegration());
            }

            $lock->release();
            if ($this->telegramIntegrationReadModel->findFailedMessagesCount() > 0) {
                $this->invoker->invoke();
            }
        }
    }
}