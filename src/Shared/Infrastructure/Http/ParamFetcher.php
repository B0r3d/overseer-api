<?php


namespace Overseer\Shared\Infrastructure\Http;


class ParamFetcher
{
    protected array $data;
    protected array $query;

    public function __construct(array $data, array $query = [])
    {
        $this->data = $data;
        $this->query = $query;
    }

    public function getDataParameter(string $parameterName, $defaultValue = null)
    {
        if (isset($this->data[$parameterName])) {
            return $this->data[$parameterName];
        }

        return $defaultValue;
    }

    public function getQueryParameter(string $parameterName, $defaultValue = null)
    {
        if (isset($this->query[$parameterName])) {
            return $this->query[$parameterName];
        }

        return $defaultValue;
    }
}