<?php
namespace ConsoleBundle\Command;

use DateTimeImmutable;
use Fadion\Fixerio\Exchange;
use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\ExchangeRate;
use SecuritiesService\Data\Database\Entity\YieldCurve;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExchangeRatesCommand extends Command
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('rates:fetch')
            ->setDescription('Import Exchange Rates')
            ->addArgument(
                'dateFrom',
                InputArgument::OPTIONAL,
                'Date to fetch from'
            )
            ->addArgument(
                'dateTo',
                InputArgument::OPTIONAL,
                'Date to fetch to'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fromDate = $input->getArgument('dateFrom');
        if (!$fromDate) {
            $this->process(new DateTimeImmutable(), $output);
            return;
        }

        $fromDate = new DateTimeImmutable($fromDate);
        $toDate = $input->getArgument('dateTo');
        if ($toDate) {
            $toDate = new DateTimeImmutable($toDate);
        } else {
            $toDate = $fromDate;
        }

        $oneDay = new \DateInterval('P1D');

        while ($fromDate <= $toDate) {
            $this->process($fromDate, $output);
            $fromDate = $fromDate->add($oneDay);
        }
        $output->writeln('Finished All');
        return;
    }

    private function process($date, OutputInterface $output)
    {
        $formattedDate = $date->format('Y-m-d');
        $output->writeln('Fetching for ' . $formattedDate);

        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $rateRepo = $this->em->getRepository('SecuritiesService:ExchangeRate');

        // find an item in the database for this date
        $rate = $rateRepo->findOneBy(
            ['date' => $date]
        );

        if ($rate) {
            $output->writeln('Rates already fetched for this date');
            return;
        }
        $output->writeln('Not yet fetched. Fetching...');

        $fixerioExchange = new Exchange();
        $fixerioExchange
            ->base(\Fadion\Fixerio\Currency::USD)
            ->historical($formattedDate);

        $result = $fixerioExchange->getResult();
        $resultDate = $result->getDate()->format('Y-m-d');

        if ($resultDate != $formattedDate) {
            $output->writeln('Date of feed is ' . $resultDate . '. Expected ' . $formattedDate . '. Stopping');
            return;
        }

        $rates = $result->getRates();

        foreach ($rates as $code => $rate) {
            $currency = $this->getCurrency($code);
            $exchangeRate = new ExchangeRate();
            $exchangeRate->setCurrency($currency);
            $exchangeRate->setDate($date);
            $exchangeRate->setRate($rate);
            $this->em->persist($exchangeRate);
            $this->em->flush();
            $output->writeln('Saved ' . $currency->getCode() . ' at ' . $rate);
        }
    }


    private function getCurrency($code)
    {
        $repo = $this->em->getRepository('SecuritiesService:Currency');
        $currency = $repo->findOneBy(
            ['code' => $code]
        );
        if (!$currency) {
            $currency = new Currency();
        }
        $currency->setCode($code);
        $this->em->persist($currency);
        $this->em->flush();
        return $currency;
    }
}
