<?php


namespace Overseer\Shared\Application;


use Overseer\Shared\Domain\Service\StringGenerator;

class RandomStringGenerator implements StringGenerator
{
    public function generate(int $length, string $prefix = '', string $postfix = ''): string
    {
        $randomString = substr(str_shuffle(MD5(microtime())), 0, $length);
        return $prefix . $randomString . $postfix;
    }
}