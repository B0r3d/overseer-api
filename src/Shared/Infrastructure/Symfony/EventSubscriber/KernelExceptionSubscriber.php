<?php


namespace Overseer\Shared\Infrastructure\Symfony\EventSubscriber;


use Overseer\Shared\Domain\Exception\NotFoundException;
use Overseer\Shared\Domain\Exception\UnauthenticatedException;
use Overseer\Shared\Domain\Exception\UnauthorizedException;
use Overseer\Shared\Domain\Exception\ValidationException;
use Overseer\Shared\Infrastructure\Http\ErrorResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class KernelExceptionSubscriber implements EventSubscriberInterface
{
    const ROUTE_PATTERN = '/\/api\//';
    private string $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                'onException'
            ],
        ];
    }

    public function onException(ExceptionEvent $event)
    {
        if ($this->env === 'dev') {
            return;
        }

        $request = $event->getRequest();

        if (!$this->isApiRoute($request)) {
            return;
        }

        $event->stopPropagation();
        $event->allowCustomResponseCode();

        $response = $this->createErrorResponse($event->getThrowable());
        $event->setResponse($response);
    }

    private function isApiRoute(Request $request): bool
    {
        return preg_match(self::ROUTE_PATTERN, $request->getPathInfo());
    }

    private function createErrorResponse(\Throwable $throwable): Response
    {
        switch(true) {
            case $throwable instanceof ValidationException:
                $statusCode = 400;
                $errorCode = $throwable->getCode();
                $message = $throwable->getMessage();
                break;
            case $throwable instanceof UnauthenticatedException:
                $statusCode = 401;
                $errorCode = $throwable->getCode();
                $message = $throwable->getMessage();
                break;
            case $throwable instanceof UnauthorizedException:
                $statusCode = 403;
                $errorCode = $throwable->getCode();
                $message = $throwable->getMessage();
                break;
            case $throwable instanceof NotFoundException:
                $statusCode = 404;
                $errorCode = $throwable->getCode();
                $message = $throwable->getMessage();
                break;
            default:
                $statusCode = 500;
                $errorCode = $throwable->getCode();
                $message = 'Unexpected error, try again later';
        }

        return new ErrorResponse($statusCode, $message, $errorCode);
    }
}