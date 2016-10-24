<?php
namespace ConsoleBundle\Command;

use DateTimeImmutable;
use Djmarland\OpenExchangeRates\Exception\ApiQuotaReachedException;
use Doctrine\ORM\EntityRepository;
use Fadion\Fixerio\Exchange;
use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\ExchangeRate;
use SecuritiesService\Data\Database\Entity\Security;
use SecuritiesService\Data\Database\Entity\YieldCurve;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrenciesUsdCommand extends Command
{
    /**
     * @var EntityRepository
     */
    protected $em;
    protected $output;
    protected $today;

    protected function configure()
    {
        $this
            ->setName('currencies:usd')
            ->setDescription('Sets the USD value against todays exchange rate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->output->writeln('Ready? Let\'s begin');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        /** @var EntityRepository $securitiesRepo */
        $securitiesRepo = $this->em->getRepository('SecuritiesService:Security');

        $this->today = new DateTimeImmutable();
        $todayFormatted = $this->today->format('Y-m-d');

        // get the exchange rates for today
        $ratesRepo = $this->em->getRepository('SecuritiesService:ExchangeRate');
        $qb = $ratesRepo->createQueryBuilder('rate')
            ->select('rate', 'currency')
            ->where('rate.date = :today')
            ->join('rate.currency', 'currency')
            ->setParameter('today', $todayFormatted);

        $result = $qb->getQuery()->getArrayResult();
        if (empty($result)) {
            $this->output->writeln('Rates not yet fetched for today. Stopping');
            return;
        }

        // sort the rates result into a simple keyed array
        $rates = [];
        foreach ($result as $rate) {
            $rates[$rate['currency']['code']] = $rate['rate'];
        }

        // count how many securities to be updated
        $qb = $securitiesRepo->createQueryBuilder('security')
            ->select('count(1)')
            ->where('security.usdCalculationDate != :today OR security.usdCalculationDate IS NULL')
            ->andWhere('security.moneyRaisedLocal IS NOT NULL')
            ->andWhere('security.startDate >= :date')
            ->setParameter('date', new DateTimeImmutable('1999-01-01'))
            ->setParameter('today', $todayFormatted);
        $unsetCount = $qb->getQuery()->getSingleScalarResult();
        $this->output->writeln('Securities not updated for todays rate: ' . $unsetCount);

        // get a batch of 100 securities
        $offset = mt_rand(0, $unsetCount);
        $this->output->writeln('Starting from random offset: ' . $offset);
        $qb = $securitiesRepo->createQueryBuilder('security')
            ->select('security', 'currency')
            ->join('security.currency', 'currency')
            ->where('security.usdCalculationDate != :today OR security.usdCalculationDate IS NULL')
            ->andWhere('security.moneyRaisedLocal IS NOT NULL')
            ->andWhere('security.startDate >= :date')
            ->orderBy('security.startDate', 'DESC')
            // exchange rates are not available pre 1999
            ->setParameter('date', new DateTimeImmutable('1999-01-01'))
            ->setParameter('today', $todayFormatted)
            ->setMaxResults(100)
            ->setFirstResult($offset);
        $securities = $qb->getQuery()->getResult();

        $updated = 0;
        $total = count($securities);

        // calculate their local currency rate for that date
        foreach ($securities as $security) {
            $this->output->writeln('Setting USD value for ' . $security->getIsin());
            /** @var Security $security */
            if ($this->fixSecurity($security, $rates)) {
                $this->output->writeln('Success');
                $updated++;
            } else {
                $this->output->writeln('Failed');
            }
        }

        $this->output->writeln(
            'Fixed ' . $updated . '/' . $total . ' - ' . ($unsetCount-$updated) . ' left'
        );
        $this->output->writeln('Done this batch. Now let me sleep');
        return;
    }

    private function fixSecurity(Security $security, array $rates)
    {
        // fetch the data needed to calculate this
        $fromCode = $security->getCurrency()->getCode();
        $moneyRaised = $security->getMoneyRaisedLocal();

        if (!$fromCode || !$moneyRaised) {
            return false;
        }

        if ($fromCode == 'GPX') {
            // lets pretend its GBP
            $fromCode = 'GBP';
            $moneyRaised = $moneyRaised / 100;
        }

        if (!isset($rates[$fromCode])) {
            $this->output->writeln(
                'Could not find exchange rates for ' . $fromCode . ' for today'
            );
            return false;
        }
        $rate = $rates[$fromCode];
        // now we need to do math!
        $usdValue = $moneyRaised / $rate;

        $security->setUsdValueNow($usdValue);
        $security->setUsdCalculationDate($this->today);
        $this->output->writeln('Converting ' . $fromCode . $moneyRaised . ' to $' . $usdValue);
        $this->em->persist($security);
        $this->em->flush();

        return true;
    }

}
