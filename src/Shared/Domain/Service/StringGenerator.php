<?php


namespace Overseer\Shared\Domain\Service;


interface StringGenerator
{
    public function generate(int $length, string $prefix = '', string $postfix = ''): string;
}