<?php


namespace Overseer\Shared\Infrastructure\CLI;


use Overseer\Shared\Domain\Bus\Event\EventBus;
use Overseer\Shared\Infrastructure\Bus\Event\EventEntity;
use Overseer\Shared\Infrastructure\Bus\Event\EventFactory;
use Overseer\Shared\Infrastructure\Bus\Event\EventRepository;
use Overseer\Shared\Infrastructure\Bus\Event\ProcessEventsCommandInvoker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;

class ProcessDomainEventsCommand extends Command
{
    protected static $defaultName = 'overseer:shared:process-domain-events';
    private EventRepository $eventRepository;
    private EventFactory $eventFactory;
    private EventBus $eventBus;
    private ProcessEventsCommandInvoker $invoker;

    public function __construct(EventRepository $eventRepository, EventFactory $eventFactory, EventBus $eventBus, ProcessEventsCommandInvoker $invoker, string $name = null)
    {
        $this->eventRepository = $eventRepository;
        $this->eventFactory = $eventFactory;
        $this->eventBus = $eventBus;
        $this->invoker = $invoker;

        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);

        $lock = $factory->createLock('domain-event-processing');

        if ($lock->acquire()) {
            $events = $this->eventRepository->findUnprocessedEvents();

            /** @var EventEntity $event */
            foreach($events as $event) {
                $domainEvent = $this->eventFactory->recreateEvent($event);
                try {
                    $this->eventBus->publish($domainEvent);
                    $event->markAsProcessed();
                    $this->eventRepository->saveEvent($event);
                } catch (\Exception $e) {
                    $event->markAsFailed($e->getMessage());
                    $this->eventRepository->saveEvent($event);
                }
            }

            $lock->release();
            if ($this->eventRepository->findUnprocessedEventsCount() > 0) {
                $this->invoker->invoke();
                return;
            }
        }

        if ($lock->acquire()) {
            $events = $this->eventRepository->findFailedEvents();

            /** @var EventEntity $event */
            foreach($events as $event) {
                $domainEvent = $this->eventFactory->recreateEvent($event);
                try {
                    $this->eventBus->publish($domainEvent);
                    $event->markAsProcessed();
                    $this->eventRepository->saveEvent($event);
                } catch (\Exception $e) {
                    $event->markAsFailed($e->getMessage());
                    $this->eventRepository->saveEvent($event);
                }
            }

            $lock->release();
            if ($this->eventRepository->findFailedEventsCount() > 0) {
                $this->invoker->invoke();
            }
        }
    }
}