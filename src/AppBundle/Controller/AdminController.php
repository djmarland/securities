<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
        $this->toView('formError', null);
        $this->toView('prevFileId', null);

        if ($request->isMethod('POST')) {
            $this->getNewFile($request);
        }

        // fetch the local copy of the file

        $filePath = $this->getParameter('kernel.cache_dir') . '/source_data/latest.csv';


        // check for differences against the last processed

        return $this->renderTemplate('admin:index');
    }

    private function getNewFile(Request $request)
    {
        $fileId = $request->get('input-file-id');
        $this->toView('prevFileId', $fileId);

        $queryString = parse_url($fileId, PHP_URL_QUERY);
        parse_str($queryString, $query);

        $id = $query['id'];

        try {
            $driveService = $this->get('app.data_services.drive');

            $data = $driveService->getFileData($id);
            $file = $driveService->getFile($data);

            $filePath = $this->getParameter('kernel.cache_dir') . '/source_data';

            if (!is_dir($filePath)) {
                mkdir($filePath);
            }
            file_put_contents($filePath . '/latest.csv', $file);
            $this->toView('formSuccess', 'Latest file was downloaded successfully');
        } catch (\Exception $e) {
            $this->toView('formError', $e->getMessage());
            return;
        }
    }
}
