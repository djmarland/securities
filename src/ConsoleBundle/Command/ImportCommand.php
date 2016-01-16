<?php
namespace ConsoleBundle\Command;

use SecuritiesService\Data\Database\Entity\Company;
use SecuritiesService\Data\Database\Entity\Country;
use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\ParentGroup;
use SecuritiesService\Data\Database\Entity\Product;
use SecuritiesService\Data\Database\Entity\Region;
use SecuritiesService\Data\Database\Entity\Security;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ContainerAwareCommand
{
    protected $em;

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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $name = $input->getArgument('file');

        $data = $this->csv_to_array($name);
        if (!$data) {
            $output->writeln('No such file');
            return;
        }

        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln('Processing');
        foreach ($data as $row) {
            $output->write('.');
            $this->processRow($row);
        }
        $output->writeln('');
        $output->writeln('Done');
    }

    function processRow($row) {
        $isin = $row['ISIN'];

        $repo = $this->em->getRepository('SecuritiesService:Security');
        $security = $repo->findOneBy(
            ['isin' => $isin]
        );
        if (!$security) {
            $security = new Security();
            $security->setIsin($isin);
        }
        $security->setName($row['SECURITY_NAME']);
        $security->setProduct($this->getProduct($row));
        $security->setCompany($this->getCompany($row));
        $security->setCurrency($this->getCurrency($row));

        $security->setMoneyRaised($row['MONEY_RAISE_GBP']);
        $startDate = DateTime::createFromFormat('U',strtotime($row['SECURITY_START_DATE']));
        $security->setStartDate($startDate);
        $endDate = ($row['MATURITY_DATE'] != 'UNDATED') ? DateTime::createFromFormat('U',strtotime($row['MATURITY_DATE'])) : null;
        $security->setMaturityDate($endDate);
        $security->setCoupon(($row['COUPON_RATE'] != 'N/A') ? floatval($row['COUPON_RATE'])/100 : null);

        $this->em->persist($security);
        $this->em->flush();
        return $security;
    }

    function getProduct($row)
    {
        $productNumber = $row['PRA_ITEM_4748'];
        $productName = $row['PRA_ITEM_4748_NAME'];
        $repo = $this->em->getRepository('SecuritiesService:Product');
        $product = $repo->findOneBy(
            ['number' => $productNumber]
        );
        if ($product) {
            return $product;
        }
        $product = new Product();
        $product->setNumber($productNumber);
        $product->setName($productName);
        $this->em->persist($product);
        $this->em->flush();
        return $product;
    }

//    function getMarket($row)
//    {
//        $name = $row['Market'];
//        $repo = $this->em->getRepository('SecuritiesService:Market');
//        $market = $repo->findOneBy(
//            ['name' => $name]
//        );
//        if ($market) {
//            return $market;
//        }
//        $market = new Market();
//        $market->setName($name);
//        $this->em->persist($market);
//        $this->em->flush();
//        return $market;
//    }

    function getCompany($row)
    {
        $name = $row['COMPANY_NAME'];
        $repo = $this->em->getRepository('SecuritiesService:Company');
        $company = $repo->findOneBy(
            ['name' => $name]
        );
        if (!$company) {
            $company = new Company();
        }
        $company->setName($name);
        $company->setCountry($this->getCountry($row));
        $company->setParentGroup($this->getParentGroup($row));
        $this->em->persist($company);
        $this->em->flush();
        return $company;
    }

    function getCurrency($row)
    {
        $code = $row['TRADING_CURRENCY'];
        $repo = $this->em->getRepository('SecuritiesService:Currency');
        $currency = $repo->findOneBy(
            ['code' => $code]
        );
        if ($currency) {
            return $currency;
        }
        $currency = new Currency();
        $currency->setCode($code);
        $this->em->persist($currency);
        $this->em->flush();
        return $currency;
    }

    function getParentGroup($row)
    {
        $name = $row['COMPANY_PARENT'];
        $repo = $this->em->getRepository('SecuritiesService:ParentGroup');
        $parentGroup = $repo->findOneBy(
            ['name' => $name]
        );
        if ($parentGroup) {
            return $parentGroup;
        }
        $parentGroup = new ParentGroup();
        $parentGroup->setName($name);
        $this->em->persist($parentGroup);
        $this->em->flush();
        return $parentGroup;
    }

//    function getSecurityType($row)
//    {
//        $name = $row['Security Description'];
//        $repo = $this->em->getRepository('SecuritiesService:SecurityType');
//        $securityType = $repo->findOneBy(
//            ['name' => $name]
//        );
//        if ($securityType) {
//            return $securityType;
//        }
//        $securityType = new SecurityType();
//        $securityType->setName($name);
//        $this->em->persist($securityType);
//        $this->em->flush();
//        return $securityType;
//    }
//
//    function getMarketSector($row)
//    {
//        $code = $row['Market Sector Code'];
//        $repo = $this->em->getRepository('SecuritiesService:MarketSector');
//        $marketSector = $repo->findOneBy(
//            ['sector_code' => $code]
//        );
//        if ($marketSector) {
//            return $marketSector;
//        }
//        $marketSector = new MarketSector();
//        $marketSector->setSectorCode($code);
//        $this->em->persist($marketSector);
//        $this->em->flush();
//        return $marketSector;
//    }
//
//    function getMarketSegment($row)
//    {
//        $code = $row['Market Segment Code'];
//        $name = $row['Market Segment'];
//        $repo = $this->em->getRepository('SecuritiesService:MarketSegment');
//        $marketSegment = $repo->findOneBy(
//            ['code' => $name]
//        );
//        if ($marketSegment) {
//            return $marketSegment;
//        }
//        $marketSegment = new MarketSegment();
//        $marketSegment->setName($name);
//        $marketSegment->setCode($code);
//        $this->em->persist($marketSegment);
//        $this->em->flush();
//        return $marketSegment;
//    }


    function getCountry($row)
    {
        $countryName = $row['COUNTRY_OF_INCORPORATION'];

        $repo = $this->em->getRepository('SecuritiesService:Country');
        $country = $repo->findOneBy(
            ['name' => $countryName]
        );
        if ($country) {
            return $country;
        }

        $country = new Country();
        $country->setName($countryName);
        $country->setRegion($this->getRegion($row));
        $this->em->persist($country);
        $this->em->flush();
        return $country;
    }

    function getRegion($row)
    {
        $name = $row['WORLD_REGION'];
        $repo = $this->em->getRepository('SecuritiesService:Region');
        $region = $repo->findOneBy(
            ['name' => $name]
        );
        if ($region) {
            return $region;
        }
        $region = new Region();
        $region->setName($name);
        $this->em->persist($region);
        $this->em->flush();
        return $region;
    }

    function csv_to_array($filename='', $delimiter=',')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }
}