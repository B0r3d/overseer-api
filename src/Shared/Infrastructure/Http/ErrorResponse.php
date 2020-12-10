<?php


namespace Overseer\Shared\Infrastructure\Http;


use Symfony\Component\HttpFoundation\JsonResponse;

final class ErrorResponse extends JsonResponse
{
    public function __construct(int $statusCode, string $errorMessage, int $errorCode)
    {
        $data = [
            'ok' => false,
            'error' => [
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
            ],
        ];

        parent::__construct($data, $statusCode);
    }
}