<?php


namespace Overseer;


use Symfony\Component\HttpFoundation\Response;

class Ping
{
    public function __invoke(): Response
    {
        return new Response('PONG', Response::HTTP_OK);
    }
}