<?php
namespace ConsoleBundle\Command;

use DateTimeImmutable;
use Djmarland\OpenExchangeRates\Exception\ApiQuotaReachedException;
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
    protected $output;

    protected function configure()
    {
        $this
            ->setName('rates:fetch')
            ->setDescription('Import Exchange Rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $now = new DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->output->writeln('Current time ' . $now->format('c'));

        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $rateRepo = $this->em->getRepository('SecuritiesService:ExchangeRate');
        $dateToUse = $now;

        // find the most recent date
        $rate = $rateRepo->findOneBy([], ['date' => 'DESC']);

        if ($rate) {
            /** @var \DateTime $rateDate */
            $rateDate = $rate->getDate();
            // if the date is today, then stop as we're up to date
            if ($rateDate->format('Y-m-d') == $now->format('Y-m-d')) {
                $this->output->writeln('Already up to date. Stopping');
                return;
            }
            // otherwise, we'll add one day to the date
            $dateToUse = $rateDate->add(new \DateInterval('P1D'));
        }

        $this->output->writeln($dateToUse->format('Y-m-d') . ' not yet fetched. Fetching...');

        $ratesClient = $this->getContainer()->get('console.services.rates');

        try {
            $result = $ratesClient->getHistorical($dateToUse);
            $date = $result->getDate();
            foreach ($result->getRates() as $currency => $value) {
                $this->addRate($currency, $value, $date);
                $this->output->writeln('Saved ' . $currency . ': ' . $value);
            }
        } catch (ApiQuotaReachedException $e) {
            $this->output->writeln('Quota reached. Doing nothing');
            return;
        }

        return;
    }

    private function addRate($code, $value, $date)
    {
        $currency = $this->getCurrency($code);
        $rate = new ExchangeRate();
        $rate->currency = $currency;
        $rate->date = $date;
        $rate->rate = $value;
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
        $currency->code = $code;
        $this->em->persist($currency);
        $this->em->flush();
        return $currency;
    }
}
