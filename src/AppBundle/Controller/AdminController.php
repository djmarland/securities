<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\ImportCommand;
use Djmarland\ISIN\Exception\InvalidISINException;
use SecuritiesService\Domain\Entity\Product;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\ISIN;
use SecuritiesService\Domain\ValueObject\UUID;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminController extends Controller
{
    use Traits\CurrenciesTableTrait;
    use Traits\SecuritiesTrait;

    protected $cacheTime = null;
    protected $bulkStats = null;

    public function initialize(Request $request)
    {
        parent::initialize($request);
        $products = $this->get('app.services.products')
            ->findAll();
        $productOptions = [];
        foreach ($products as $product) {
            /** @var Product $product */
            $productOptions[] = [
                'value' => $product->getNumber(),
                'label' => $product->getName() . ' (' . $product->getNumber() . ')',
            ];
        }

        $this->toView('productOptions', $productOptions);

        // get the current status of the bulk upload
        $this->bulkStats = $this->getBulkStats();
        $this->toView('bulkStats', $this->bulkStats);
    }

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
        $statsExchangeRates = $this->get('app.services.exchange_rates')->countAll();
        $statsUsers = $this->get('app.services.users')->countAll();

        $this->toView('statsAll', number_format($statsAll));
        $this->toView('statsActive', number_format($statsActive));
        $this->toView('statsMatured', number_format($statsMatured));
        $this->toView('statsIssuers', number_format($statsIssuers));
        $this->toView('statsGroups', number_format($statsGroups));
        $this->toView('statsSectors', number_format($statsSectors));
        $this->toView('statsIndustries', number_format($statsIndustries));
        $this->toView('statsExchangeRates', number_format($statsExchangeRates));
        $this->toView('statsUsers', number_format($statsUsers));

        return $this->renderTemplate('admin:index', 'Admin');
    }

    public function dataAction()
    {
        $this->toView('activeTab', 'data');

        return $this->renderTemplate('admin:data', 'Admin - Data');
    }

    public function bulkUploadAction()
    {
        $file = $this->request->getContent();
        if (empty($file)) {
            $this->toView('status', 'error', true);
            $this->toView('message', 'No file receieved', true);
            return $this->renderJSON();
        }

        $filePath = $this->getParameter('kernel.cache_dir') . '/bulk/';
        $fileName = $filePath . 'bulk-upload.csv';

        if (!is_dir($filePath)) {
            mkdir($filePath);
        }

        // save the file as-is first
        file_put_contents($fileName, $file);

        // re-read the CSV one line at a time, saving it into chunks
        $headings = null;
        $totalLines = 0;
        $currentChunkLines = 0;
        $currentChunkCount = 1;
        $maxPerChunk = 100;
        $currentChunk = null;

        $handle = fopen($fileName, 'r');
        while (($line = fgets($handle)) !== false) {
            // grab and store the headings from the first list
            if (!$headings) {
                $headings = $line;
                continue;
            }

            if (!$currentChunk) {
                // create a new chunk
                $currentChunk = $headings;
            }

            $totalLines++;
            $currentChunkLines++;
            $currentChunk .= $line;

            if ($currentChunkLines == $maxPerChunk) {
                // save the chunk
                $fileName = $filePath . 'bulk-chunk-' . $currentChunkCount . '.csv';
                file_put_contents($fileName, $currentChunk);

                // reset for the next chunk
                $currentChunk = null;
                $currentChunkLines = 0;
                $currentChunkCount++;
            }
        }

        fclose($handle);

        // save the last chunk if it's not empty (in case last row wasn't a full chunk)
        if ($currentChunk) {
            $fileName = $filePath . 'bulk-chunk-' . $currentChunkCount . '.csv';
            file_put_contents($fileName, $currentChunk);
        }

        // now store the stats
        $bulkStats = (object) [
            'totalToProcess' => $totalLines,
            'totalProcessed' => 0,
            'totalToProcessFormatted' => number_format($totalLines),
            'totalProcessedFormatted' => 0,
            'totalBatches' => $currentChunkCount,
            'lastBatchCompleted' => 0,
        ];

        file_put_contents($filePath . 'bulk-stats.json', json_encode($bulkStats));

        $this->toView('status', 'ok', true);
        $this->toView('message', 'File uploaded', true);
        $this->toView('stats', $bulkStats, true);
        return $this->renderJSON();
    }

    public function securitiesCheckAction()
    {
        $isin = $this->request->get('isin');
        $status = null;
        $message = null;


        try {
            \Djmarland\ISIN\ISIN::validate($isin);
            $isin = new ISIN($isin);

            $security = $this->get('app.services.securities')
                ->fetchByIsin($isin);

            $status = 'found';

            $this->toView('security', $security->jsonSerialize(true), true);
        } catch (InvalidISINException $e) {
            $status = 'error';
            $message = $e->getMessage();
        } catch (EntityNotFoundException $e) {
            $status = 'new';
        }

        $this->toView('status', $status, true);
        $this->toView('message', $message, true);
        return $this->renderJSON();
    }

    public function searchAction()
    {
        $search = trim($this->request->get('q'));
        $type = trim($this->request->get('type'));

        switch ($type) {
            case 'issuer':
                $results = $this->get('app.services.issuers')
                    ->search($search);
                break;
            default:
                throw new HttpException(400, 'Missing type');
        }

        $this->toView('results', $results, true);
        return $this->renderJSON();
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

    public function bulkProcessAction()
    {
        if (!$this->request->isMethod('POST')) {
            throw new HttpException(405, 'Must be POST');
        }

        $stats = $this->bulkStats;
        if (!$stats) {
            throw new HttpException(404, 'Nothing to process');
        }

        $failures = [];
        $newBatchNumber = $stats->lastBatchCompleted + 1;
        $securities = [];
        if ($newBatchNumber <= $stats->totalBatches) {
            // get the file
            $filePath = $this->getParameter('kernel.cache_dir') . '/bulk/';
            $fileName = $filePath . 'bulk-chunk-' . $newBatchNumber . '.csv';
            $csv = file_get_contents($fileName);

            // process the CSV and get the ISINs back out of it
            $data = $this->csvToArray($csv);

            $command = new ImportCommand();
            $command->setContainer($this->container);
            $isins = [];
            foreach ($data as $row) {
                try {
                    // put in database
                    $entity = $command->single($row);
                    $isins[] = (string) $entity->getIsin();
                } catch (\Exception $e) {
                    // this isin failed for some reason. we need to store it
                    $failures[] = (object) [
                        'isin' => $row['ISIN'],
                        'reason' => $e->getMessage(),
                    ];
                }
                $stats->totalProcessed++;
            }

            // now let's fetch back all the securities we just saved
            $securities = $this->get('app.services.securities')->fetchMultipleByIsin($isins);

            // resave the updated status
            $stats->totalProcessedFormatted = number_format($stats->totalProcessed);
            $stats->lastBatchCompleted = $newBatchNumber;
            $stats->failures = array_merge($stats->failures ?? [], $failures);

            $statusFileName = $filePath . 'bulk-stats.json';
            file_put_contents($statusFileName, json_encode($stats));
        }

        $this->toView('stats', $stats, true);
        $this->toView('securities', $securities, true);
        return $this->renderJSON();
    }

    public function lseListAction()
    {
        $this->toView('activeTab', 'new');

//        $perPage = 100;
//        $currentPage = $this->getCurrentPage();

        $service = $this->get('app.services.lse_announcements');

        $announcements = $service->findLatest();

        // @todo - pagination
        //$total = $service->countIncomplete();

        $this->toView('announcements', $announcements);

        return $this->renderTemplate('admin:lse_list', 'New Securities - Admin');
    }

    public function lseShowAction()
    {
        $uuid = $this->request->get('lse_id');

        $service = $this->get('app.services.lse_announcements');

        try {
            $announcement = $service->findByUUID(UUID::createFromString($uuid));
        } catch (EntityNotFoundException $e) {
            throw new HttpException(404, 'Announcement ' . $uuid . ' does not exist.');
        }


        if ($this->request->isMethod('POST')) {
            $done = $this->request->request->has('submit-done');
            $error = $this->request->request->has('submit-error');
            if ($done || $error) {
                if ($done) {
                    $service->markAsDone($announcement);
                } elseif ($error) {
                    $service->markAsError($announcement);
                }
                return $this->redirectToRoute('admin_lse_list', [], 302);
            }
        }


        $announcementSource = $this->get('app.announcements')
            ->getSource($announcement->getLink());

        $this->toView('announcementSource', $announcementSource);
        $this->toView('announcement', $announcement);

        // @todo
        // - Fetch the source feed (cache it in file cache)
        // - identify possible securities
        // - find those ISINs in the database (to see if already processed)

        $this->toView('activeTab', 'new');
        return $this->renderTemplate(
            'admin:lse_show',
            'LSE Announcement ' . $announcement->getLink() . ' - Admin'
        );
    }

    public function processSecurityAction()
    {
        if (!$this->request->isMethod('POST')) {
            throw new HttpException(405, 'Must be POST');
        }

        try {
            $formData = $this->request->request->all();
            if (empty($formData)) {
                // must be JSON
                $formData = json_decode($this->request->getContent(), true);
            }

            $command = new ImportCommand();
            $command->setContainer($this->container);
            $command->single($formData);
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

        return $this->renderTemplate('admin:compare', 'Compare - Admin');
    }

    public function interestingAction()
    {
        $this->toView('activeTab', 'interesting');
        $securitiesService = $this->get('app.services.securities');
        $total = $securitiesService->countInteresting();
        $presenters = null;

        $perPage = 20;
        $currentPage = $this->getCurrentPage();

        if ($total) {
            $securities = $securitiesService
                ->findInteresting(
                    $perPage,
                    $currentPage
                );
            $presenters = $this->securitiesToPresenters($securities);
        }
        $this->setPagination(
            $total,
            $currentPage,
            $perPage
        );

        $this->toView('securities', $presenters);

        return $this->renderTemplate(
            'admin:interesting',
            'Interesting Securities - Admin'
        );
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
                'total' => $service->countAll(),
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
                $coupon,
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

    private function getBulkStats()
    {
        $fileName = $this->getParameter('kernel.cache_dir') . '/bulk/bulk-stats.json';
        if (file_exists($fileName)) {
            return json_decode(file_get_contents($fileName));
        }
        return null;
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
