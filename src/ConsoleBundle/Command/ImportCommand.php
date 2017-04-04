<?php
namespace ConsoleBundle\Command;

use DateInterval;
use DateTimeImmutable;
use Djmarland\ISIN\ISIN;
use Ramsey\Uuid\Uuid;
use SecuritiesService\Data\Database\Entity\Company;
use SecuritiesService\Data\Database\Entity\Country;
use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\Industry;
use SecuritiesService\Data\Database\Entity\ParentGroup;
use SecuritiesService\Data\Database\Entity\Product;
use SecuritiesService\Data\Database\Entity\Region;
use SecuritiesService\Data\Database\Entity\Sector;
use SecuritiesService\Data\Database\Entity\Security;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    protected $em;

    protected $productsData = [
        1 => 'Non-dated capital resources',
        40 => 'Unsecured senior securities',
        41 => 'Dated subordinated securities',
        42 => 'Structured notes',
        43 => 'Covered bonds',
        51 => 'Securitisations',
    ];

    protected $products = [];
    protected $output;

    public function single($row)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        return $this->processRow($row);
    }

    public function singleIssuer($row)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $company = $this->getRowValue($row, 'COMPANY_NAME');
        if ($company) {
            $this->getCompany($row);
        }
    }

    protected function configure()
    {
        $this
            ->setName('isin:import')
            ->setDescription('Import a CSV')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to input file'
            )
            ->addOption(
                'row',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Row Number to start from'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $name = $input->getArgument('file');
        $startRow = $input->getOption('row');

        $data = $this->csvToArray($name);
        if (!$data) {
            $output->writeln('No such file');
            return;
        }

        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->setProducts();

        $this->output->writeln('Processing');
        $totalCount = count($data);
        $progress = new ProgressBar($this->output, $totalCount);

        $progress->start();
        if ($startRow) {
            $data = array_slice($data, $startRow-1);
            $progress->setProgress($startRow);
        }
        foreach ($data as $row) {
            $this->processRow($row);
            // clear doctrine cache after each row (or memory runs out)
            $this->em->clear();
            $progress->advance();
        }
        $progress->finish();
        $this->output->writeln('');
        $this->output->writeln('Done');
    }

    private function setProducts()
    {
        $this->output->writeln('Creating Products');
        $repo = $this->em->getRepository('SecuritiesService:Product');
        foreach ($this->productsData as $number => $name) {
            $product = $repo->findOneBy(
                ['number' => $number]
            );
            if ($product) {
                $this->output->writeln($number . ' exists');
            } else {
                $this->output->writeln('Creating ' . $number);
                $product = new Product();
            }

            $product->number = $number;
            $product->name = $name;
            $this->em->persist($product);
            $this->em->flush();

            $this->products[$number] = $product;
        }
        $this->output->writeln('Products created');
    }

    private function getRowValue($row, $key)
    {
        if (isset($row[$key])) {
            $value = trim(utf8_encode($row[$key]));
            if (!empty($value)) {
                return $value;
            }
        }
        return null;
    }

    private function processRow($row)
    {
        $isin = $this->getRowValue($row, 'ISIN');
        if ($this->isUnset($isin)) {
            throw new \Exception('An ISIN is required');
        }
        ISIN::validate($isin);

        $repo = $this->em->getRepository('SecuritiesService:Security');
        $security = $repo->findOneBy(
            ['isin' => $isin]
        );
        if (!$security) {
            $security = new Security();
            $security->isin = $isin;
        }
        $security->exchange = ''; // wrong - to be removed

        $name = $this->getRowValue($row, 'SECURITY_NAME');
        if ($name) {
            $security->name = $name;
        }

        $source = $this->getRowValue($row, 'SOURCE');
        if ($source) {
            $security->source = $source;
        }

//        $excelZeroPoint = new DateTimeImmutable('1900-01-01T12:00:00');
//
//        $startDate = $excelZeroPoint->add(new DateInterval('P' . $row['SECURITY_START_DATE'] . 'D'));
//        $maturityDate = null;
//        if (is_numeric($row['MATURITY_DATE'])) {
//            $maturityDate = $excelZeroPoint->add(new DateInterval('P' . $row['MATURITY_DATE'] . 'D'));
//        }

        $startDate = $this->getRowValue($row, 'SECURITY_START_DATE');
        if (!empty($startDate)) {
            $startDate = DateTimeImmutable::createFromFormat('d/m/Y', $startDate);
        } else {
            $startDate = new DateTimeImmutable('1970-01-01T00:00:00Z');
        }
        $security->startDate = $startDate;
        $maturityDate = $this->getRowValue($row, 'MATURITY_DATE');
        if ($maturityDate) {
            if (preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $maturityDate)) {
                $maturityDate = DateTimeImmutable::createFromFormat('d/m/Y', $maturityDate);
            } else {
                $maturityDate = null;
            }
            $security->maturityDate = $maturityDate;
        }

        $moneyRaised = $this->getRowValue($row, 'MONEY_RAISED_GBP');
        if ($moneyRaised) {
            $moneyRaised = str_replace(',', '', $moneyRaised);
            if (!is_numeric($moneyRaised)) {
                throw new \Exception('Money Raised (GBP) is not a number');
            }
            $security->moneyRaised = $moneyRaised;
        }

        $moneyRaised = $this->getRowValue($row, 'MONEY_RAISED_LOCAL');
        if ($moneyRaised) {
            $moneyRaised = str_replace(',', '', $moneyRaised);
            if (!is_numeric($moneyRaised)) {
                throw new \Exception('Money Raised (Local) is not a number');
            }
            $security->moneyRaisedLocal = $moneyRaised;
        }

        $coupon = $this->getRowValue($row, 'COUPON_RATE');
        if ($coupon) {
            if (strtolower($coupon) != 'n/a') {
                $couponValue = floatval($row['COUPON_RATE']);
                if (strpos($this->getRowValue($row, 'COUPON_RATE'), '%') !== false) {
                    $couponValue = $couponValue / 100;
                }
                $coupon = $couponValue;
            } else {
                $coupon = null;
            }
            $security->coupon = $coupon;
        }

        $margin = $this->getRowValue($row, 'MARGIN');
        if ($margin) {
            if (strtolower($margin) != 'n/a') {
                $marginValue = floatval($row['MARGIN']);
                if (strpos($this->getRowValue($row, 'MARGIN'), '%') !== false) {
                    $marginValue = $marginValue / 100;
                }
                $margin = $marginValue;
            } else {
                $margin = null;
            }
            $security->margin = $margin;
        }


//        $security->market = ($row['MARKET']);
//        $security->tidm = ($row['TIDM']);
//        $security->description = ($row['SECURITY_DESCRIPTION']);

        $product = $this->getRowValue($row, 'PRA_ITEM_4748');
        if ($product) {
            $security->product = $this->getProduct($row);
        }

        $currency = $this->getRowValue($row, 'TRADING_CURRENCY');
        if ($currency) {
            $security->currency = $this->getCurrency($row);
        }

        $company = $this->getRowValue($row, 'COMPANY_NAME');
        if ($company) {
            $security->company = $this->getCompany($row);
        }

        $this->em->persist($security);
        $this->em->flush();
        return $security;
    }

    private function isUnset($value)
    {
        return (empty($value) || in_array(strtolower($value), [
            '-',
            '#n/a',
            'n/a',
            'other',
            'unclassified',
        ]));
    }

    private function getProduct($row)
    {
        $repo = $this->em->getRepository('SecuritiesService:Product');
        $productNumber = $this->getRowValue($row, 'PRA_ITEM_4748');
        $product = $repo->findOneBy(
            ['number' => $productNumber]
        );
        if (!isset($product)) {
            throw new \Exception('No such product ' . $productNumber);
        }
        return $product;
    }

    private function getCompany($row)
    {
        $name = $this->getRowValue($row, 'COMPANY_NAME');
        $id = $this->getRowValue($row, 'COMPANY_ID');
        $repo = $this->em->getRepository('SecuritiesService:Company');
        $company = null;

        if ($id) {
            $uuid = Uuid::fromString($id);
            $company = $repo->findOneBy(
                ['id' => $uuid]
            );
        } else {
            $company = $repo->findOneBy(
                ['name' => $name]
            );
        }
        if (!$company) {
            $company = new Company();
        }
        $company->name = $name;

        $country = $this->getRowValue($row, 'COUNTRY_OF_INCORPORATION');
        if ($country) {
            $company->country = $this->getCountry($row);
        }

        $parent = $this->getRowValue($row, 'COMPANY_PARENT');
        if ($parent) {
            $company->parentGroup = $this->getParentGroup($row);
        }

        $this->em->persist($company);
        $this->em->flush();
        return $company;
    }

    private function getCurrency($row)
    {
        $code = $this->getRowValue($row, 'TRADING_CURRENCY');
        $repo = $this->em->getRepository('SecuritiesService:Currency');
        $currency = $repo->findOneBy(
            ['code' => $code]
        );
        if (!$currency) {
            $currency = new Currency();
        }
        $currency->code = $code;
        $this->em->persist($currency);
        $this->em->flush();
        return $currency;
    }

    private function getParentGroup($row)
    {
        $name = $this->getRowValue($row, 'COMPANY_PARENT');
        if ($this->isUnset($name)) {
            return null;
        }

        $repo = $this->em->getRepository('SecuritiesService:ParentGroup');
        $parentGroup = $repo->findOneBy(
            ['name' => $name]
        );
        if (!$parentGroup) {
            $parentGroup = new ParentGroup();
        }
        $parentGroup->name = $name;

        $sector = $this->getRowValue($row, 'ICB_SECTOR');
        if ($sector) {
            $parentGroup->sector = $this->getSector($row);
        }
        $this->em->persist($parentGroup);
        $this->em->flush();
        return $parentGroup;
    }

    private function getSector($row)
    {
        $name = $this->getRowValue($row, 'ICB_SECTOR');
        if ($this->isUnset($name)) {
            return null;
        }
        $repo = $this->em->getRepository('SecuritiesService:Sector');
        $sector = $repo->findOneBy(
            ['name' => $name]
        );
        if (!$sector) {
            $sector = new Sector();
        }
        $sector->name = $name;
        $sector->industry = $this->getIndustry($row);
        $this->em->persist($sector);
        $this->em->flush();
        return $sector;
    }

    private function getIndustry($row)
    {
        $name = $this->getRowValue($row, 'ICB_INDUSTRY');
        if ($this->isUnset($name)) {
            return null;
        }
        $repo = $this->em->getRepository('SecuritiesService:Industry');
        $industry = $repo->findOneBy(
            ['name' => $name]
        );
        if (!$industry) {
            $industry = new Industry();
        }
        $industry->name = $name;
        $this->em->persist($industry);
        $this->em->flush();
        return $industry;
    }


    private function getCountry($row)
    {
        $countryName = $this->getRowValue($row, 'COUNTRY_OF_INCORPORATION');
        if ($this->isUnset($countryName)) {
            return null;
        }

        $repo = $this->em->getRepository('SecuritiesService:Country');
        $country = $repo->findOneBy(
            ['name' => $countryName]
        );
        if ($country) {
            return $country;
        }

        $country = new Country();
        $country->name = $countryName;
        $country->region = $this->getRegion($row);
        $this->em->persist($country);
        $this->em->flush();
        return $country;
    }

    private function getRegion($row)
    {
        $name = $this->getRowValue($row, 'WORLD_REGION');
        if ($this->isUnset($name)) {
            return null;
        }
        $repo = $this->em->getRepository('SecuritiesService:Region');
        $region = $repo->findOneBy(
            ['name' => $name]
        );
        if ($region) {
            return $region;
        }
        $region = new Region();
        $region->name = $name;
        $this->em->persist($region);
        $this->em->flush();
        return $region;
    }
}
