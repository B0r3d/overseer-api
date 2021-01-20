<?php


namespace Overseer\Shared\Infrastructure\Http;


use Overseer\Shared\Domain\ValueObject\Cookie;

class CookieManager
{
    public function addCookie(Cookie $cookie, string $path = '', string $domain = '', bool $secure = false): void
    {
        setcookie(
            $cookie->getName(),
            $cookie->getValue(),
            $cookie->getExpiryTimestamp(),
            $path,
            $domain,
            $secure,
            $cookie->isHttpOnly()
        );
    }

    public function removeCookie(string $cookieName)
    {
        setcookie($cookieName, '', time() - 3600);
    }

    public function getCookie(string $name): ?string
    {
        if (!isset($_COOKIE[$name])) {
            return null;
        }

        return $_COOKIE[$name];
    }
}