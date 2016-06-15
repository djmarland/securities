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

        $securitiesService = $this->get('app.services.securities');

        $statsAll = $securitiesService->countAll();
        $statsActive = $securitiesService->count();
        $statsMatured = $securitiesService->countMatured();
        $statsIssuers = $this->get('app.services.issuers')->countAll();
        $statsGroups = $this->get('app.services.groups')->countAll();
        $statsSectors = $this->get('app.services.sectors')->countAll();
        $statsIndustries = $this->get('app.services.industries')->countAll();

        $this->toView('statsAll', number_format($statsAll));
        $this->toView('statsActive', number_format($statsActive));
        $this->toView('statsMatured', number_format($statsMatured));
        $this->toView('statsIssuers', number_format($statsIssuers));
        $this->toView('statsGroups', number_format($statsGroups));
        $this->toView('statsSectors', number_format($statsSectors));
        $this->toView('statsIndustries', number_format($statsIndustries));

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

                $data = [
                    'COMPANY_ID' => !empty($editId) ? $editId : null,
                    'COMPANY_NAME' => $editName,
                    'COUNTRY_OF_INCORPORATION' => !empty($editCountry) ? $editCountry : '-',
                    'COMPANY_PARENT' => !empty($editGroup) ? $editGroup : '-',
                ];
                try {
                    $command = new ImportCommand();
                    $command->setContainer($this->container);
                    $command->singleIssuer($data);
                    $this->toView('formSuccess', 'Issuer was updated successfully');
                } catch (\Exception $e) {
                    $this->toView('formSuccess', 'There was an error when trying to update the issuer');
                }
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

    public function processSecurityAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            throw new HttpException(405, 'Must be POST');
        }

        try {
            $command = new ImportCommand();
            $command->setContainer($this->container);
            $command->single($request->request->all());
            $this->toView('message', 'ok', true);
        } catch (\Exception $e) {
            $this->toView('error', $e->getMessage(), true);
        }
        return $this->renderTemplate('json');
    }

    public function settingsAction(Request $request)
    {
        // settings were fetched globally
        $this->toView('message', null);
        $this->toView('activeTab', 'settings');

        if ($request->isMethod('POST')) {
            try {
                // save
                $service = $this->get('app.services.config');

                $settings = [
                    'siteTitle' => $request->get('field-siteTitle', ''),
                    'siteHostName' => $request->get('field-siteHostName', ''),
                    'siteTagLine' => $request->get('field-siteTagLine', ''),
                    'adsInDevMode' => (bool) $request->get('field-adsInDevMode', false),
                ];
                $service->setSettings($settings);

                $features = $request->get('feature-flag', []);
                $service->setActiveFeatures($features);

                $this->toView('message', 'Saved');

                // re-fetch global settings
                $this->initAppConfig();
                $this->masterViewPresenter->updateConfig($this->appConfig);
            } catch (\Exception $e) {
                $this->toView('message', $e->getMessage());
            }
        }

        $this->setTitle('Settings - Admin');
        return $this->renderTemplate('admin:settings');

    }

    public function exportAction(Request $request)
    {
        // settings were fetched globally
        $this->toView('message', null);
        $this->toView('activeTab', 'export');

        $path = $this->getParameter('kernel.cache_dir') . '/export.json';
        $latestExport = null;
        if (file_exists($path)) {
            $latestExport = json_decode(file_get_contents($path));
        }

        $percentage = 0;
        $processed = 0;
        $total = '?';
        $download = null;
        if ($latestExport) {
            $processed = $latestExport->processed;
            $total = $latestExport->total;
            $percentage = round(($processed / $total) * 100, 1);
            if ($percentage == 100) {
                $download = $latestExport->fileName;
            }

        }

        $this->toView('showExportButton', (
            !$latestExport || $latestExport->processed === $latestExport->total
        ));
        $this->toView('download', $download);
        $this->toView('processed', $processed);
        $this->toView('total', $total);
        $this->toView('percentage', $percentage);
        $this->setTitle('Settings - Export');
        return $this->renderTemplate('admin:export');
    }

    public function exportProcessAction(Request $request)
    {
        $path = $this->getParameter('kernel.cache_dir') . '/export.json';
        $export = null;
        if (file_exists($path)) {
            $export = json_decode(file_get_contents($path));
        }

        if (!$export || $export->processed == $export->total) {
            // need to make a new one
            $export = (object) [
                'fileName' => 'bob.csv',
                'processed' => 0,
                'total' => 120
            ];

            // create the data file with the headings
        }

        // get X number of rows
        $offset = $export->processed;
        $limit = 10;

        // get the data

        // add the rows to the file

        // update the status file
        $export->processed = $export->processed + $limit;
        $this->toView('export', $export, true);

        file_put_contents($path, json_encode($export));

        return $this->renderJSON();
    }



    private function setCsvData($data)
    {
        foreach ($data as $i => $row) {
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
        $fp = fopen('php://temp', 'r+');
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
