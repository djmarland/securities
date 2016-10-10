<?php
namespace ConsoleBundle\Command;

use Djmarland\OpenExchangeRates\Exception\ApiQuotaReachedException;
use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\ExchangeRate;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HistoricalRatesCommand extends Command
{
    protected $em;
    protected $output;

    protected function configure()
    {
        $this
            ->setName('currencies:historical')
            ->setDescription('Import Exchange Rates')
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Date to fetch from'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->output->writeln('Starting');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $ratesClient = $this->getContainer()->get('console.services.rates_historical');
        $now = new \DateTimeImmutable();

        $limiterFilename = $this->getContainer()->getParameter('kernel.cache_dir') . '/api-limiter';
        if (file_exists($limiterFilename)) {
            $expiryTime = new \DateTimeImmutable(file_get_contents($limiterFilename));
            if ($expiryTime > $now) {
                $this->output->writeln('Limited use due to API Key limit. Expires on ' . $expiryTime->format('c'));
                return;
            }
        }
        $repo = $this->em->getRepository('SecuritiesService:ExchangeRate');

        $inputDate = $input->getArgument('date');
        if ($inputDate) {
            $dateToUse = new \DateTimeImmutable($inputDate);
            $this->output->writeln('Manual date: '. $dateToUse->format('c'));
        } else {

            // get the oldest exchange rate we've already fetched
            $rate = $repo->findOneBy([], ['date' => 'ASC']);

            if (!$rate) {
                $this->output->writeln('No starting point. Exiting');
                return;
            }

            /** @var \DateTime $rateDate */
            $rateDate = $rate->getDate();
            $this->output->writeln('Oldest Rate: '. $rateDate->format('c'));
            // if the date is earlier than 2000, exit
            $endAt = new \DateTimeImmutable('1999-01-01');
            if ($endAt > $rateDate) {
                $this->output->writeln('Jan 1999 reached. Doing nothing');
                return;
            }

            $dateToUse = $rateDate->sub(new \DateInterval('P1D'));
        }


        try {
            $this->output->writeln('Fetching ' . $dateToUse->format('c'));
            $result = $ratesClient->getHistorical($dateToUse);
            // convert the date to UTC for database storage
            $date = $result->getDate();
            $date = $date->setTimezone(new \DateTimeZone('UTC'));
            $this->output->writeln('Date found ' .$date->format('c'));
            foreach($result->getRates() as $currency => $value) {
                $this->addRate($currency, $value, $date);
            }
        } catch (ApiQuotaReachedException $e) {
            // create an limiter time
            $expiryTime = $now->add(new \DateInterval('P1D'));
            file_put_contents($limiterFilename, $expiryTime->format('c'));
            $this->output->writeln('Quota reached. Doing nothing');
            return;
        }

        $this->output->writeln('Finished All');
        return;
    }

    private function addRate($code, $value, $date)
    {
        $currency = $this->getCurrency($code);
        // check we don't already have one for this date

        $repo = $this->em->getRepository('SecuritiesService:ExchangeRate');
        $existingRate = $repo->findOneBy(['currency' => $currency, 'date' => $date]);
        if ($existingRate) {
            $this->output->writeln('Skipped ' . $code . ': ' . $value . ' - already fetched');
            return;
        }

        $rate = new ExchangeRate();
        $rate->setCurrency($currency);
        $rate->setDate($date);
        $rate->setRate($value);
        $this->em->persist($rate);
        $this->em->flush();

        $this->output->writeln('Saved ' . $code . ': ' . $value);
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
