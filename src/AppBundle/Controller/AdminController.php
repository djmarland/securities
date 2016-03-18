<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\ImportCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
        $this->toView('formSuccess', null);
        $this->toView('csv', '[]');

        if ($request->isMethod('POST')) {
            $file = $request->request->get('submit-file');
            $text = $request->request->get('submit-text');

            if (isset($file)) {
                $file = $request->files->get('csv-file');
                if ($file) {
                    $filename = 'csv-upload.csv';
                    $dir = $this->getParameter('kernel.cache_dir');
                    $file->move($dir, $filename);
                    $csv = file_get_contents($this->getParameter('kernel.cache_dir') . '/' . $filename);
                    $csvData = $this->csvToArray($csv);
                    $this->setCsvData($csvData);
                    $this->toView('formSuccess', 'CSV data has been added to the form. Check, then click save');
                } else {
                    $this->toView('formSuccess', 'No CSV data was found in file upload');
                }
            } elseif (isset($text)) {
                $csv = $request->request->get('csv-text', null);
                if ($csv) {
                    $csvData = $this->csvToArray($csv);
                    $this->setCsvData($csvData);
                    $this->toView('formSuccess', 'CSV data has been added to the form. Check, then click save');
                } else {
                    $this->toView('formSuccess', 'No CSV data was found in CSV field');
                }
            } else {
                $this->toView('formSuccess', 'No data input');
            }
        }

        return $this->renderTemplate('admin:index');
    }


    public function processAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            throw new HttpException(405, 'Must be POST');
        }

        try {
            $command = new ImportCommand();
            $command->setContainer($this->container);
            $command->single($request->request->all());
            $this->toView('message', 'ok');
        } catch (\Exception $e) {
            $this->toView('error', $e->getMessage());
        }
        return $this->renderTemplate('json');
    }



    private function setCsvData($data)
    {
        foreach($data as $i => $row) {
            foreach ($row as $key => $value) {
                $data[$i][$key] = utf8_encode($value);
            }
        }
        $this->toView('csv', json_encode($data));
    }

    private function csvToArray($csv)
    {
        $header = null;
        $data = [];
        $fp = fopen('php://temp','r+');
        fwrite($fp, $csv);
        rewind($fp); //rewind to process CSV
        while (($row = fgetcsv($fp, 0)) !== false) {
            if (!$header) {
                $header = $row;
            } else {
                $data[] = array_combine($header, $row);
            }
        }
        return $data;
    }
}
