<?php
namespace ConsoleBundle\Command;

use SecuritiesService\Data\Database\Entity\Company;
use SecuritiesService\Data\Database\Entity\Country;
use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\Fsa04748;
use SecuritiesService\Data\Database\Entity\FsaBucket;
use SecuritiesService\Data\Database\Entity\Market;
use SecuritiesService\Data\Database\Entity\MarketSector;
use SecuritiesService\Data\Database\Entity\MarketSegment;
use SecuritiesService\Data\Database\Entity\Region;
use SecuritiesService\Data\Database\Entity\Security;
use SecuritiesService\Data\Database\Entity\SecurityType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        $security->setName($row['Security Name']);
        $security->setCountry($this->getCountry($row));
        $security->setFsa04748($this->getFsa04748($row));
        $security->setMarket($this->getMarket($row));
        $security->setCompany($this->getCompany($row));
        $security->setSecurityType($this->getSecurityType($row));
        $security->setMarketSegment($this->getMarketSegment($row));
        $security->setMarketSector($this->getMarketSector($row));
        $security->setCurrency($this->getCurrency($row));
        $security->setFsaContractualBucket($this->getBucket($row, 'CONTRACTUAL_FSA_BUCKET'));
        $security->setFsaResidualBucket($this->getBucket($row, 'RESIDUAL_FSA_BUCKET'));

        $security->setTidm($row['TIDM']);
        $security->setMoneyRaised($row[' Money Raised (£m) ']);
        $startDate = DateTime::createFromFormat('d/m/Y',$row['Start Date']);
        $security->setStartDate($startDate);
        $endDate = ($row['MATURITY_DATE'] != 'UNDATED') ? DateTime::createFromFormat('d/m/Y',$row['MATURITY_DATE']) : null;
        $security->setMaturityDate($endDate);
        $security->setCoupon(($row['COUPON'] != 'N/A') ? $row['COUPON'] : null);
        $security->setWeighting(($row['WEIGHTING'] != 'N/A') ? $row['WEIGHTING'] : null);
        $security->setContractualMaturity(($row['CONTRACTUAL_MATURITY']) != 'UNDATED' ? $row['CONTRACTUAL_MATURITY'] : null);
        $security->setIssueMonth($row['ISSUE_MONTH']);

        $this->em->persist($security);
        $this->em->flush();
        return $security;
    }

    function getBucket($row, $key)
    {
        $value = $row[$key];
        $yearsFrom = null;
        $yearsTo = null;
        switch ($value) {
            case '> 2 years <= 5 years':
                $yearsFrom = 2;
                $yearsTo = 5;
                break;

            case '> 5 years <= 10 years':
                $yearsFrom = 5;
                $yearsTo = 10;
                break;

            case '> 10 years':
                $yearsFrom = 10;
                break;

            case '> 6 months <= 1 year':
                $yearsFrom = 2;
                $yearsTo = 5;
                break;

            // @todo - set real values
            default :
                $yearsFrom = 0;
                $yearsTo = 0;

        }

        $repo = $this->em->getRepository('SecuritiesService:FsaBucket');
        $bucket = $repo->findOneBy(
            ['years_from' => $yearsFrom, 'years_to' => $yearsTo]
        );
        if ($bucket) {
            return $bucket;
        }
        $bucket = new FsaBucket();
        $bucket->setYearsFrom($yearsFrom);
        $bucket->setYearsTo($yearsTo);
        $this->em->persist($bucket);
        $this->em->flush();
        return $bucket;
    }

    function getFsa04748($row)
    {
        $line = $row['FSA04748_LINE'];
        $lineName = $row['FSA04748_LINE_NAME'];
        $repo = $this->em->getRepository('SecuritiesService:Fsa04748');
        $fsa04748 = $repo->findOneBy(
            ['line' => $line]
        );
        if ($fsa04748) {
            return $fsa04748;
        }
        $fsa04748 = new Fsa04748();
        $fsa04748->setLine($line);
        $fsa04748->setName($lineName);
        $this->em->persist($fsa04748);
        $this->em->flush();
        return $fsa04748;
    }

    function getMarket($row)
    {
        $name = $row['Market'];
        $repo = $this->em->getRepository('SecuritiesService:Market');
        $market = $repo->findOneBy(
            ['name' => $name]
        );
        if ($market) {
            return $market;
        }
        $market = new Market();
        $market->setName($name);
        $this->em->persist($market);
        $this->em->flush();
        return $market;
    }

    function getCompany($row)
    {
        $name = $row['Company Name'];
        $repo = $this->em->getRepository('SecuritiesService:Company');
        $company = $repo->findOneBy(
            ['name' => $name]
        );
        if ($company) {
            return $company;
        }
        $company = new Company();
        $company->setName($name);
        $this->em->persist($company);
        $this->em->flush();
        return $company;
    }

    function getCurrency($row)
    {
        $code = $row['Trading Currency'];
        $repo = $this->em->getRepository('SecuritiesService:Currency');
        $company = $repo->findOneBy(
            ['code' => $code]
        );
        if ($company) {
            return $company;
        }
        $company = new Currency();
        $company->setCode($code);
        $this->em->persist($company);
        $this->em->flush();
        return $company;
    }

    function getSecurityType($row)
    {
        $name = $row['Security Description'];
        $repo = $this->em->getRepository('SecuritiesService:SecurityType');
        $securityType = $repo->findOneBy(
            ['name' => $name]
        );
        if ($securityType) {
            return $securityType;
        }
        $securityType = new SecurityType();
        $securityType->setName($name);
        $this->em->persist($securityType);
        $this->em->flush();
        return $securityType;
    }

    function getMarketSector($row)
    {
        $code = $row['Market Sector Code'];
        $repo = $this->em->getRepository('SecuritiesService:MarketSector');
        $marketSector = $repo->findOneBy(
            ['sector_code' => $code]
        );
        if ($marketSector) {
            return $marketSector;
        }
        $marketSector = new MarketSector();
        $marketSector->setSectorCode($code);
        $this->em->persist($marketSector);
        $this->em->flush();
        return $marketSector;
    }

    function getMarketSegment($row)
    {
        $code = $row['Market Segment Code'];
        $name = $row['Market Segment'];
        $repo = $this->em->getRepository('SecuritiesService:MarketSegment');
        $marketSegment = $repo->findOneBy(
            ['code' => $name]
        );
        if ($marketSegment) {
            return $marketSegment;
        }
        $marketSegment = new MarketSegment();
        $marketSegment->setName($name);
        $marketSegment->setCode($code);
        $this->em->persist($marketSegment);
        $this->em->flush();
        return $marketSegment;
    }


    function getCountry($row)
    {
        $countryName = $row['Country of Incorporation'];

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
        $name = $row['World Region'];
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