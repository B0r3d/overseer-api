<?php


namespace Overseer\Shared\Infrastructure\Http;


use Symfony\Component\HttpFoundation\JsonResponse;

final class SuccessResponse extends JsonResponse
{
    public function __construct($payload = [], int $status = 200, array $headers = [], bool $json = false)
    {
        $data = [
            'ok' => true,
            'payload' => $payload,
        ];

        parent::__construct($data, $status, $headers, $json);
    }
}