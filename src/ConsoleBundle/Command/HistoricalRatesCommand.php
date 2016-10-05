<?php
namespace ConsoleBundle\Command;

use Djmarland\OpenExchangeRates\Exception\ApiQuotaReachedException;
use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\ExchangeRate;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HistoricalRatesCommand extends Command
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('currencies:historical')
            ->setDescription('Import Exchange Rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $ratesClient = $this->getContainer()->get('console.services.rates_historical');
        $now = new \DateTimeImmutable();

        $limiterFilename = $this->getContainer()->getParameter('kernel.cache_dir') . '/api-limiter';
        if (file_exists($limiterFilename)) {
            $expiryTime = new \DateTimeImmutable(file_get_contents($limiterFilename));
            if ($expiryTime > $now) {
                $output->writeln('Limited use due to API Key limit. Expires on ' . $expiryTime->format('c'));
                return;
            }
        }

        // get the oldest exchange rate we've already fetched
        $repo = $this->em->getRepository('SecuritiesService:ExchangeRate');
        $rate = $repo->findBy([], ['date' => 'ASC']);

        if (empty($rate)) {
            $output->writeln('No starting point. Exiting');
            return;
        }

        $rate = reset($rate);
        /** @var \DateTime $rateDate */
        $rateDate = $rate->getDate();

        $output->writeln('Oldest Rate: '. $rateDate->format('d-m-Y'));

        // if the date is earlier than 2000, exit
        $endAt = new \DateTimeImmutable('2000-01-01');
        if ($endAt > $rateDate) {
            $output->writeln('Jan 2000 reached. Doing nothing');
            return;
        }

        $dateToUse = $rateDate->sub(new \DateInterval('P1D'));

        try {
            $output->writeln('Fetching ' . $dateToUse->format('d-m-Y'));
            $result = $ratesClient->getHistorical($dateToUse);
            $date = $result->getDate();
            foreach($result->getRates() as $currency => $value) {
                $this->addRate($currency, $value, $date);
                $output->writeln('Saved ' . $currency . ': ' . $value);
            }
        } catch (ApiQuotaReachedException $e) {
            // create an limiter time
            $expiryTime = $now->add(new \DateInterval('P1D'));
            file_put_contents($limiterFilename, $expiryTime->format('c'));
            $output->writeln('Quota reached. Doing nothing');
            return;
        }

        $output->writeln('Finished All');
        return;
    }

    private function addRate($code, $value, $date)
    {
        $currency = $this->getCurrency($code);
        $rate = new ExchangeRate();
        $rate->setCurrency($currency);
        $rate->setDate($date);
        $rate->setRate($value);
        $this->em->persist($rate);
        $this->em->flush();
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
