<?php


namespace Overseer;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Ping extends AbstractController
{
    public function __invoke(): Response
    {
        return new Response('PONG', Response::HTTP_OK);
    }
}