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

class CurrenciesLocalCommand extends Command
{
    /**
     * @var EntityRepository
     */
    protected $em;
    protected $output;

    protected function configure()
    {
        $this
            ->setName('currencies:local')
            ->setDescription('Set local currency values for all securities');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->output->writeln('Ready? Let\'s begin');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        /** @var EntityRepository $securitiesRepo */
        $securitiesRepo = $this->em->getRepository('SecuritiesService:Security');

        // count how many securities affected
        $qb = $securitiesRepo->createQueryBuilder('security')
            ->select('count(1)')
            ->where('security.moneyRaisedLocal IS NULL')
            ->andWhere('security.startDate >= :date')
            ->setParameter('date', new DateTimeImmutable('1999-01-01'));
        $nullCount = $qb->getQuery()->getSingleScalarResult();
        $this->output->writeln('Securities with null local currency: ' . $nullCount);

        // get a batch of 100 securities (ordered by most recently created)
        $qb = $securitiesRepo->createQueryBuilder('security')
            ->select('security', 'currency')
            ->join('security.currency', 'currency')
            ->where('security.moneyRaisedLocal IS NULL')
            ->andWhere('security.startDate >= :date')
            ->orderBy('security.startDate', 'DESC')
            // exchange rates are not available pre 1999
            ->setParameter('date', new DateTimeImmutable('1999-01-01'))
            ->setMaxResults(100)
            ->setFirstResult(mt_rand(0, $nullCount));
        $securities = $qb->getQuery()->getResult();

        $fixedSecuritiesCounter = 0;

        // calculate their local currency rate for that date
        foreach ($securities as $security) {
            $this->output->writeln('Fixing ' . $security->getIsin());
            /** @var Security $security */
            if ($this->fixSecurity($security)) {
                $this->output->writeln('Success');
                $fixedSecuritiesCounter++;
            } else {
                $this->output->writeln('Failed');
            }
        }

        $this->output->writeln(
            'Fixed ' . $fixedSecuritiesCounter . '/100 - ' . ($nullCount-$fixedSecuritiesCounter) . ' left'
        );
        $this->output->writeln('Done this batch. Now let me sleep');
        return;
    }

    private function fixSecurity(Security $security)
    {
        // fetch the data needed to calculate this
        $date = $security->getStartDate();
        $fromCode = 'GBP';
        $toCode = $security->getCurrency()->getCode();
        $moneyRaised = $security->getMoneyRaised();

        if (!$date || !$fromCode || !$toCode || !$moneyRaised) {
            return false;
        }

        if ($fromCode == $toCode) {
            $security->setMoneyRaisedLocal($moneyRaised);
            $this->output->writeln('Saving simple GBP to GBP');
            $this->em->persist($security);
            $this->em->flush();
            return true;
        }

        if ($toCode == 'GPX') {
            // GPX is penny prices, so in that currency will be 100 times bigger
            $security->setMoneyRaisedLocal($moneyRaised * 100);
            $this->output->writeln('Saving GBX');
            $this->em->persist($security);
            $this->em->flush();
            return true;
        }

        $currenciesRepo = $this->em->getRepository('SecuritiesService:Currency');
        $gbp = $currenciesRepo->findOneBy(['code' => 'GBP']);
        $local = $security->getCurrency();

        $ratesRepo = $this->em->getRepository('SecuritiesService:ExchangeRate');
        // for that day get the relevant exchange rates
        $gbpRate = $ratesRepo->findOneBy(['currency' => $gbp, 'date' => $date]);
        $localRate = $ratesRepo->findOneBy(['currency' => $local, 'date' => $date]);

        if (!$gbpRate || !$localRate) {
            $this->output->writeln(
                'Could not find exchange rates for GBP or ' . $local->getCode() . ' on ' . $date->format('c')
            );
            return false;
        }

        // now we need to do math!
        // convert the GBP value to USD
        $usdValue = $moneyRaised / $gbpRate->getRate();
        // convert this usd value to local
        $localValue = $usdValue * $localRate->getRate();

        $security->setMoneyRaisedLocal($localValue);
        $this->output->writeln('Saving as ' . $local->getCode() . ':' . $localValue);
        $this->em->persist($security);
        $this->em->flush();

        return true;
    }

}
