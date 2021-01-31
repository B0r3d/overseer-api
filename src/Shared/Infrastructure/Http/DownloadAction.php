<?php


namespace Overseer\Shared\Infrastructure\Http;


use Overseer\Shared\Domain\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

class DownloadAction extends AbstractController
{
    public function __invoke(Request $request)
    {
        $path = urldecode($request->query->get('file_path'));

        if(!file_exists($path)) {
            throw new NotFoundException();
        }

        $file = new \SplFileInfo($path);
        $response = new BinaryFileResponse($file, 200, [
            'Content-Disposition' => 'filename=errors.' . $file->getExtension(),
            'Content-Type' => 'application/octet-stream',
        ]);

        $response->deleteFileAfterSend(true);

        return $response;
    }
}