<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\ImportCommand;
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
        $this->cacheTime = null;

        // if the diff.json already exists and is not empty,
        // return the data from it

        $filePath = $this->getParameter('kernel.cache_dir') . '/source_data/';

        $diffPath = $filePath . 'diff.json';
        if (file_exists($diffPath)) {
            $diff = json_decode(file_get_contents($diffPath));

            $this->toView('diffCount', count($diff));
            if (empty($diff)) {
                return $this->renderTemplate('admin:diff-done');
            }
            $this->toView('diffData', array_slice($diff, 0, 20));
            return $this->renderTemplate('admin:diff-status');
        }

        // if there is no latest.csv
        // return message saying, no source file
        $latestPath = $filePath . 'latest.csv';
        if (!file_exists($latestPath)) {
            return $this->renderTemplate('admin:diff-no-source');
        }


        $previous = [];
        $previousPath = $filePath . 'processed_hashes.json';
        if (file_exists($previousPath)) {
            $previous = json_decode(file_get_contents($previousPath));
        }

        $latestHashes = [];
        $diffLines = [];
        $latest = $this->csvToArray($latestPath);

        foreach ($latest as $row) {
            foreach ($row as $k => $value) {
                $row[$k] = utf8_encode($value);
            }
            $hash = md5(serialize($row));
            $latestHashes[] = $hash;
            if (!in_array($hash, $previous)) {
                $diffLines[] = $row;
            }
        }
        if (count($diffLines) > 1000) {
            return $this->renderTemplate('admin:diff-too-many');
        }

        file_put_contents($filePath . 'latest_hashes.json', json_encode($latestHashes, JSON_PRETTY_PRINT));
        $json = json_encode($diffLines, JSON_PRETTY_PRINT);
        file_put_contents($filePath . 'diff.json', $json);
        $this->toView('diffCount', count($diffLines));
        if (empty($diffLines)) {
            return $this->renderTemplate('admin:diff-done');
        }
        $this->toView('diffData', array_slice($diffLines, 0, 20));
        return $this->renderTemplate('admin:diff-status');
    }

    public function processAction(Request $request)
    {
        $this->cacheTime = null;
        // fetch the diff list
        // take the first X items from the top
        // process them
        // return the updated diff list (diffAction)

        // if the diff list is empty, move latest.csv and latest_hash.json
        // into processed.csv and processed_hash.json

        $filePath = $this->getParameter('kernel.cache_dir') . '/source_data/';

        $diffPath = $filePath . 'diff.json';
        if (file_exists($diffPath)) {
            $diff = json_decode(file_get_contents($diffPath));

            $this->toView('diffCount', count($diff));
            if (empty($diff)) {
                return $this->renderTemplate('admin:diff-done');
            }

            $command = new ImportCommand();
            $command->setContainer($this->container);

            for ($i = 0;$i<10;$i++) {
                // todo - process the row
                if (!empty($diff)) {
                    $row = array_shift($diff);
                    $command->single((array) $row);
                }
            }

            // check if the diff is now empty. if so move files
            if (empty($diff)) {
                rename($filePath . '/latest.csv', $filePath . '/processed.csv');
                rename($filePath . '/latest_hashes.json', $filePath . '/processed_hashes.json');
            }

            // re-save the diff
            file_put_contents($diffPath, json_encode($diff, JSON_PRETTY_PRINT));

            $this->toView('diffCount', count($diff));
            $this->toView('diffData', array_slice($diff, 0, 20));
            return $this->renderTemplate('admin:diff-status');
        }
        return $this->renderTemplate('admin:diff-done');
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

            // delete the diff file
            if (file_exists($filePath . '/diff.json')) {
                unlink($filePath . '/diff.json');
            }

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
