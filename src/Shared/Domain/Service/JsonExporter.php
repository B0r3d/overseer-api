<?php


namespace Overseer\Shared\Domain\Service;


class JsonExporter
{
    public function export(array $data): string
    {
        return json_encode($data);
    }
}