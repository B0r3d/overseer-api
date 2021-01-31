<?php


namespace Overseer\Project\Infrastructure\Http\Action;


use Overseer\Project\Domain\Dto\ProjectErrorResource;
use Overseer\Project\Domain\Dto\ProjectErrorsListResource;
use Overseer\Project\Domain\Entity\Error;
use Overseer\Project\Domain\Entity\StacktraceException;
use Overseer\Project\Domain\Query\GetProjectErrorsQuery;
use Overseer\Project\Domain\Service\ProjectReadModel;
use Overseer\Project\Domain\ValueObject\Exception;
use Overseer\Shared\Domain\Service\CsvExporter;
use Overseer\Shared\Domain\Service\JsonExporter;
use Overseer\Shared\Domain\ValueObject\PaginatedQueryResult;
use Overseer\Shared\Domain\ValueObject\Uuid;
use Overseer\Shared\Infrastructure\Http\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetProjectErrorsAction extends AbstractAction
{
    private ProjectReadModel $projectReadModel;
    private string $projectRootDir;

    public function __construct(ProjectReadModel $projectReadModel, string $projectRootDir)
    {
        $this->projectReadModel = $projectReadModel;
        $this->projectRootDir = $projectRootDir;
    }

    public function __invoke(Request $request): Response
    {
        $subject = $this->getUser();

        $paramFetcher = $this->getParamFetcher($request);

        if (!$paramFetcher->getQueryParameter('format')) {
            $query = new GetProjectErrorsQuery(
                $request->get('_project_id'),
                $subject->getUsername(),
                $paramFetcher->getQueryParameter('page', 1),
                [
                    'search' => $paramFetcher->getQueryParameter('search', ''),
                    'date_from' => $paramFetcher->getQueryParameter('date_from', ''),
                    'date_to' => $paramFetcher->getQueryParameter('date_to', ''),
                ],
                [
                    $paramFetcher->getQueryParameter('sort_by', '')
                ]
            );

            /** @var PaginatedQueryResult $result */
            $result = $this->ask($query);

            $items = $result->getItems();
            $resources = [];

            /** @var Error $error */
            foreach ($items as $error) {
                $resources[] = new ProjectErrorsListResource($error);
            }

            $result = new PaginatedQueryResult(
                $resources,
                $result->getCount(),
                $result->getPage()
            );

            return $this->respondWithOk($result);
        } else {
            $format = $paramFetcher->getQueryParameter('format');
            $errors = $this->projectReadModel->getAllErrors($request->get('_project_id'), $subject->getUsername(), [
                'search' => $paramFetcher->getQueryParameter('search', ''),
                'date_from' => $paramFetcher->getQueryParameter('date_from', ''),
                'date_to' => $paramFetcher->getQueryParameter('date_to', ''),
            ]);

            $resources = [];
            /** @var Error $error */
            foreach ($errors as $error) {
                $resources[] = new ProjectErrorResource($error);
            }

            switch (strtoupper($format)) {
                case 'JSON':
                    $exporter = new JsonExporter();
                    $fileContent = $exporter->export($resources);
                    $extension = 'json';
                    break;
                case 'CSV':
                    $exporter = new CsvExporter();
                    $data = [];

                    /** @var Error $error */
                    foreach ($errors as $error) {
                        $row = [
                            'id' => $error->getId()->value(),
                            'project_id' => $error->getProject()->getId()->value(),
                            'occurred_at' => $error->getOccurredAt()->getTimestamp(),
                            'class' => $error->getException()->getClass(),
                            'error_code' => $error->getException()->getErrorCode(),
                            'error_message' => $error->getException()->getErrorMessage(),
                            'line' => $error->getException()->getLine(),
                            'file' => $error->getException()->getFile(),
                        ];

                        $stacktrace = [];

                        /** @var Exception $stacktraceException */
                        foreach ($error->getStacktrace() as $stacktraceException) {
                            $stacktrace[] = [
                                'class' => $stacktraceException->getClass(),
                                'error_code' => $stacktraceException->getErrorCode(),
                                'error_message' => $stacktraceException->getErrorMessage(),
                                'line' => $stacktraceException->getLine(),
                                'file' => $stacktraceException->getFile(),
                            ];
                        }

                        $row['stacktrace'] = json_encode($stacktrace);
                        $data[] = $row;
                    }

                    $headers = [
                        'ID',
                        'Project ID',
                        'Occurred at',
                        'class',
                        'error_code',
                        'error_message',
                        'line',
                        'file',
                        'stacktrace',
                    ];

                    $fileContent = $exporter->export($headers, $data);
                    $extension = 'csv';
                    break;
                default:
                    $exporter = new JsonExporter();
                    $fileContent = $exporter->export($resources);
                    $extension = 'json';
            }

            $filePath = $this->projectRootDir . '/var/data/downloads/' . Uuid::random()->value() . '.' . $extension;
            @mkdir($this->projectRootDir . '/var/data/downloads', 0777, true);

            file_put_contents($filePath, $fileContent);

            return $this->respondWithOk([
                'file' => $filePath,
            ]);
        }

    }
}