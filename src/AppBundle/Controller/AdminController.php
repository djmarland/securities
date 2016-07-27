<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\ImportCommand;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Domain\ValueObject\UUID;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminController extends Controller
{
    use Traits\CurrenciesTableTrait;

    protected $cacheTime = null;

    public function indexAction()
    {
        $this->buildCurrenciesTable();

        $this->toView('activeTab', 'dashboard');

        $securitiesService = $this->get('app.services.securities');

        $statsAll = $securitiesService->countAll();
        $statsActive = $securitiesService->count();
        $statsMatured = $securitiesService->countMatured();
        $statsIssuers = $this->get('app.services.issuers')->countAll();
        $statsGroups = $this->get('app.services.groups')->countAll();
        $statsSectors = $this->get('app.services.sectors')->countAll();
        $statsIndustries = $this->get('app.services.industries')->countAll();
        $statsUsers = $this->get('app.services.users')->countAll();

        $this->toView('statsAll', number_format($statsAll));
        $this->toView('statsActive', number_format($statsActive));
        $this->toView('statsMatured', number_format($statsMatured));
        $this->toView('statsIssuers', number_format($statsIssuers));
        $this->toView('statsGroups', number_format($statsGroups));
        $this->toView('statsSectors', number_format($statsSectors));
        $this->toView('statsIndustries', number_format($statsIndustries));
        $this->toView('statsUsers', number_format($statsUsers));

        return $this->renderTemplate('admin:index', 'Admin');
    }

    public function securitiesAction()
    {
        $this->toView('formSuccess', null);
        $this->toView('csv', '[]');
        $this->toView('activeTab', 'securities');

        if ($this->request->isMethod('POST')) {
            $file = $this->request->request->get('submit-file');
            $text = $this->request->request->get('submit-text');

            if (isset($file)) {
                $file = $this->request->files->get('csv-file');
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
                $csv = $this->request->request->get('csv-text', null);
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

        return $this->renderTemplate('admin:securities', 'Securities - Admin');
    }

    public function issuersAction()
    {
        $this->toView('formSuccess', null);
        $this->toView('activeTab', 'issuers');

        // if post, process the delete
        if ($this->request->isMethod('POST')) {
            $delete = $this->request->request->get('delete-id');
            $editName = $this->request->request->get('field-name');
            if ($delete) {
                $this->toView('formSuccess', 'There was an error when trying to delete');
                $success = $this->get('app.services.issuers')->deleteWithId(UUID::createFromString($delete));
                if ($success) {
                    $this->toView('formSuccess', 'Deleted successfully');
                }
            } elseif ($editName) {
                $editGroup = $this->request->request->get('field-group');
                $editCountry = $this->request->request->get('field-country');
                $editId = $this->request->request->get('field-id');

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

        return $this->renderTemplate('admin:issuers', 'Issuers - Admin');
    }

    public function processSecurityAction()
    {
        if (!$this->request->isMethod('POST')) {
            throw new HttpException(405, 'Must be POST');
        }

        try {
            $command = new ImportCommand();
            $command->setContainer($this->container);
            $command->single($this->request->request->all());
            $this->toView('message', 'ok', true);
        } catch (\Exception $e) {
            $this->toView('error', $e->getMessage(), true);
        }
        return $this->renderTemplate('json');
    }

    public function settingsAction()
    {
        // settings were fetched globally
        $this->toView('message', null);
        $this->toView('activeTab', 'settings');

        if ($this->request->isMethod('POST')) {
            try {
                // save
                $service = $this->get('app.services.config');

                $settings = [
                    'siteTitle' => $this->request->get('field-siteTitle', ''),
                    'siteHostName' => $this->request->get('field-siteHostName', ''),
                    'siteTagLine' => $this->request->get('field-siteTagLine', ''),
                    'adsInDevMode' => (bool) $this->request->get('field-adsInDevMode', false),
                ];
                $service->setSettings($settings);

                $features = $this->request->get('feature-flag', []);
                $service->setActiveFeatures($features);

                $this->toView('message', 'Saved');

                // re-fetch global settings
                $this->initAppConfig();
                $this->masterViewPresenter->updateConfig($this->appConfig);
            } catch (\Exception $e) {
                $this->toView('message', $e->getMessage());
            }
        }

        return $this->renderTemplate('admin:settings', 'Settings - Admin');

    }

    public function compareAction()
    {
        $this->toView('activeTab', 'compare');

        $sourceIsins = $this->get('app.services.securities')
            ->findAllIsins();

        $this->toView('sourceIsins', $sourceIsins);

        return $this->renderTemplate('admin:compare'. 'Compare - Admin');

    }

    public function exportAction()
    {
        // settings were fetched globally
        $this->toView('message', null);
        $this->toView('activeTab', 'export');

        $latestExport = $this->getExportFile();

        $percentage = 0;
        $processed = 0;
        $total = '?';
        $download = false;
        if ($latestExport) {
            $processed = $latestExport->processed;
            $total = $latestExport->total;
            $percentage = round(($processed / $total) * 100, 1);
            if ($percentage == 100) {
                $download = true;
            }

        }

        $this->toView('showExportButton', (
            !$latestExport || $latestExport->processed === $latestExport->total
        ));
        $this->toView('download', $download);
        $this->toView('processed', $processed);
        $this->toView('total', $total);
        $this->toView('percentage', $percentage);
        return $this->renderTemplate('admin:export', 'Export - Admin');
    }

    public function exportProcessAction()
    {
        $export = $this->getExportFile();

        $service = $this->get('app.services.securities');

        if (!$export || $export->processed == $export->total) {
            // count the number of ISINs

            // need to make a new one
            $export = (object) [
                'fileName' => time() . '.csv',
                'processed' => 0,
                'total' => $service->countAll()
            ];

            // create the data file with the headings
            $headings = [
                'ISIN',
                'SECURITY_NAME',
                'PRA_ITEM_4748',
                'COMPANY_NAME',
                'COUNTRY_OF_INCORPORATION',
                'COMPANY_PARENT',
                'ICB_SECTOR',
                'ICB_INDUSTRY',
                'MONEY_RAISED_GBP',
                'TRADING_CURRENCY',
                'SECURITY_START_DATE',
                'MATURITY_DATE',
                'COUPON_RATE',
            ];

            $this->addRow($headings, $export->fileName);
        }

        // get X number of rows
        $limit = 250;
        $page = ($export->processed / $limit) + 1;

        // get the data
        $results = $service->findAllFull($limit, $page);

        // add the rows to the file
        foreach ($results as $result) {
            /** @var $result Security */
            $isin = (string) $result->getIsin();
            $name = (string) $result->getName();
            $product = $result->getProduct() ? (string) $result->getProduct()->getNumber() : null;
            $companyName = null;
            $companyCountry = null;
            $parentCompanyName = null;
            $sectorName = null;
            $industryName = null;


            if ($result->getCompany()) {
                $company = $result->getCompany();
                $companyName = (string) $company->getName();
                if ($company->getCountry()) {
                    $companyCountry = (string) $company->getCountry()->getName();
                }

                if ($company->getParentGroup()) {
                    $parentCompany = $company->getParentGroup();
                    $parentCompanyName = (string) $parentCompany->getName();

                    if ($parentCompany->getSector()) {
                        $sector = $parentCompany->getSector();
                        $sectorName = (string) $sector->getName();

                        if ($sector->getIndustry()) {
                            $industryName = (string) $sector->getIndustry()->getName();
                        }
                    }

                }
            }

            $moneyRaised = (string) $result->getMoneyRaised();
            $currency = $result->getCurrency() ? (string) $result->getCurrency()->getCode() : null;

            $dateFormat = 'Y-m-d';

            $startDate = (string) $result->getStartDate()->format($dateFormat);
            $endDate = $result->getMaturityDate() ? (string) $result->getMaturityDate()->format($dateFormat) : null;

            $coupon = (string) $result->getCoupon();

            $values = [
                $isin,
                $name,
                $product,
                $companyName,
                $companyCountry,
                $parentCompanyName,
                $sectorName,
                $industryName,
                $moneyRaised,
                $currency,
                $startDate,
                $endDate,
                $coupon
            ];
            $this->addRow($values, $export->fileName);
        }

        // update the status file
        $export->processed = $export->processed + $limit;
        if ($export->processed > $export->total) {
            $export->processed = $export->total;
        }
        $this->toView('export', $export, true);

        file_put_contents($this->getParameter('kernel.cache_dir') . '/export.json', json_encode($export));

        return $this->renderJSON();
    }

    public function exportDownloadAction()
    {
        $latestExport = $this->getExportFile();
        if (!$latestExport) {
            throw new HttpException('No download file exists', 404);
        }

        $path = $this->getParameter('kernel.cache_dir') . '/' . $latestExport->fileName;
        if (!file_exists($path)) {
            throw new HttpException('No download file exists', 404);
        }

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
        return $response;
    }

    private function getExportFile()
    {
        $path = $this->getParameter('kernel.cache_dir') . '/export.json';
        $export = null;
        if (file_exists($path)) {
            $export = json_decode(file_get_contents($path));
        }
        return $export;
    }

    private function addRow($values, $filename)
    {
        $path = $this->getParameter('kernel.cache_dir') . '/' . $filename;
        $fp = fopen($path, 'a');
        fputcsv($fp, $values);
        fclose($fp);
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
