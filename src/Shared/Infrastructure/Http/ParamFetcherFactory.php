<?php


namespace Overseer\Shared\Infrastructure\Http;


use Symfony\Component\HttpFoundation\Request;

class ParamFetcherFactory
{
    public function createFetcher(Request $request)
    {
        $query = $request->query->all();
        switch($request->headers->get('Content-Type')) {
            case 'application/json':
                $data = json_decode($request->getContent(), true);
                break;
            case 'multipart/form-data':
                $data = $request->request->all();
                break;
            default:
                $data = json_decode($request->getContent(), true);
        }

        if (!is_array($data)) {
            $data = [];
        }

        return new ParamFetcher($data, $query);
    }
}