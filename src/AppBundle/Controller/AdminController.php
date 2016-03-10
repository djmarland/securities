<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
        $this->toView('formError', null);
        $this->toView('formSuccess', null);
        $this->toView('prevFileId', null);

        if ($request->isMethod('POST')) {
            $this->getNewFile($request);
        }

        // fetch the diff list



        // fetch the local copy of the file

       // $filePath = $this->getParameter('kernel.cache_dir') . '/source_data/latest.csv';


        // check for differences against the last processed

        return $this->renderTemplate('admin:index');
    }

    public function diffAction(Request $request)
    {
        // if the diff.json already exists and is not empty,
        // return the data from it

        // if there is no latest.csv
        // return message saying, no source file



        $filePath = $this->getParameter('kernel.app') . '/source_data/';

        $previous = [];
        $previousPath = $filePath . 'processed.csv';
        if (file_exists($previous)) {
            $previous = $this->csvToArray($previousPath);
        }

        $diff = [];
        $diffPath = $filePath . 'diff.csv';
        if (file_exists($diffPath)) {
            $diff = json_decode(file_get_contents($diffPath);
        }

        $latestPath = $filePath . 'latest.csv';
        $latest = $this->csvToArray($latestPath);

        // generate an array of hashes from the processed.csv (probably already done)

        // loop through the latest generating hashes for each row (md5(serialise($row))

        // if the hash of the row is not in the processed hash table, add the row to the diff list

    }

    public function processAction(Request $request)
    {
        // fetch the diff list
        // take the first X items from the top
        // process them
        // return the updated diff list (diffAction)

        // if the diff list is empty, move latest.csv and latest_hash.json
        // into processed.csv and processed_hash.json
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

            $filePath = $this->getParameter('kernel.app') . '/source_data';

            if (!is_dir($filePath)) {
                mkdir($filePath);
            }
            file_put_contents($filePath . '/latest.csv', $file);
            $this->generateDiff();
            $this->toView('formSuccess', 'Latest file was downloaded successfully');
        } catch (\Exception $e) {
            $this->toView('formError', $e->getMessage());
            return;
        }
    }

    protected function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
}
