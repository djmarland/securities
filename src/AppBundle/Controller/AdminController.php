<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\ImportCommand;
use SecuritiesService\Domain\ValueObject\UUID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminController extends Controller
{
    protected $cacheTime = null;

    public function indexAction(Request $request)
    {
        $this->toView('activeTab', 'dashboard');
        return $this->renderTemplate('admin:index');
    }

    public function securitiesAction(Request $request)
    {
        $this->toView('formSuccess', null);
        $this->toView('csv', '[]');
        $this->toView('activeTab', 'securities');

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

        $noIssuer = $this->get('app.services.securities')->findAllWithoutIssuer();
        $this->toView('noIssuerCount', count($noIssuer));
        $this->toView('noIssuer', $noIssuer);

        return $this->renderTemplate('admin:securities');
    }

    public function issuersAction(Request $request)
    {
        $this->toView('formSuccess', null);
        $this->toView('activeTab', 'issuers');

        // if post, process the delete
        if ($request->isMethod('POST')) {
            $delete = $request->request->get('delete-id');
            $editName = $request->request->get('field-name');
            if ($delete) {
                $this->toView('formSuccess', 'There was an error when trying to delete');
                $success = $this->get('app.services.issuers')->deleteWithId(UUID::createFromString($delete));
                if ($success) {
                    $this->toView('formSuccess', 'Deleted successfully');
                }
            } elseif ($editName) {
                $editGroup = $request->request->get('field-group');
                $editCountry = $request->request->get('field-country');
                $editId = $request->request->get('field-id');

                $success = $this->get('app.services.issuers')->createOrUpdate(
                    $editId,
                    $editName,
                    $editGroup,
                    $editCountry
                );
            }
        }

        // get a list of issuers with no securities
        $noSecurities = $this->get('app.services.issuers')->findAllWithoutSecurities();
        $this->toView('noSecuritiesCount', count($noSecurities));
        $this->toView('noSecurities', $noSecurities);

        // get a list of issuers with no parent
        $noParent = $this->get('app.services.issuers')->findAllWithoutGroup();
        $this->toView('noParentCount', count($noParent));
        $this->toView('noParent', $noParent);


        return $this->renderTemplate('admin:issuers');
    }

    public function processIssuerAction(Request $request)
    {
        $this->toView('message', 'ok');
        return $this->renderTemplate('json');
    }

    public function processSecurityAction(Request $request)
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
