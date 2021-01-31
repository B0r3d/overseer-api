<?php


namespace Overseer\Shared\Domain\Service;


use Overseer\Project\Domain\Dto\ProjectErrorResource;

class CsvExporter
{
    public function export(array $headers, array $data): string
    {
        $fileContent = "";

        foreach($headers as $index => $header) {
            $fileContent .= '"' . $header . '"';
            if ($index !== count($headers) - 1) {
                $fileContent .= ',';
            } else {
                $fileContent .= PHP_EOL;
            }
        }

        foreach ($data as $row) {
            foreach ($row as $index => $item) {
                $fileContent .= '"' . $item . '"';
                if ($index !== count($row) - 1) {
                    $fileContent .= ',';
                } else {
                    $fileContent .= PHP_EOL;
                }
            }
        }

        return $fileContent;
    }
}