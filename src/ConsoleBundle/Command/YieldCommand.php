<?php
namespace ConsoleBundle\Command;

use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\ParentGroup;
use SecuritiesService\Data\Database\Entity\YieldCurve;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class YieldCommand extends Command
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('isin:yield')
            ->setDescription('Import a CSV')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to input file'
            );
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

        // delete all previous data
        $this->em->createQuery('DELETE FROM SecuritiesService:YieldCurve')->execute();

        $output->writeln('Processing');
        foreach ($data as $row) {
            $output->write('.');
            $this->processRow($row);
        }
        $output->writeln('');
        $output->writeln('Done');
    }

    private function processRow($row)
    {
        $type = $row['CURVE_TYPE'];
        $year = $row['YEAR'];

        $data = $this->getDataPoints($row);

        $yieldCurve = new YieldCurve();
        $yieldCurve->setType($type);
        $yieldCurve->setYear($year);
        $yieldCurve->setDataPoints($data);
        $yieldCurve->setParentGroup($this->getParentGroup($row));
        $yieldCurve->setCurrency($this->getCurrency($row));
        $this->em->persist($yieldCurve);
        $this->em->flush();
    }

    private function getDataPoints($row)
    {
        $data = [];
        foreach (range(1, 30) as $point) {
            $value = trim($row[$point], '%');
            if (empty($value)) {
                $value = null;
            } else {
                $value = (float) $value;
            }
            $data[$point] = $value;
        }
        return json_encode($data);
    }

    private function getParentGroup($row)
    {
        $name = $row['COMPANY_SECTOR_INDUSTRY'];
        $repo = $this->em->getRepository('SecuritiesService:ParentGroup');
        $parentCompany = $repo->findOneBy(
            ['name' => $name]
        );
        if ($parentCompany) {
            return $parentCompany;
        }
        return null;
        // @todo - this should throw as it's bad data
        throw new \Exception('No such parent group ' . $name);
    }

    private function getCurrency($row)
    {
        $code = $row['CURRENCY'];
        $repo = $this->em->getRepository('SecuritiesService:Currency');
        $currency = $repo->findOneBy(
            ['code' => $code]
        );
        if ($currency) {
            return $currency;
        }
        throw new \Exception('No such currency ' . $code);
    }
}
