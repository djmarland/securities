<?php
namespace ConsoleBundle\Command;

use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\Curve;
use SecuritiesService\Data\Database\Entity\ParentGroup;
use SecuritiesService\Data\Database\Entity\YieldCurve;
use SecuritiesService\Domain\ValueObject\CurvePoints;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCurvesCommand extends Command
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('isin:curves')
            ->setDescription('Generate some random curve data')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Type'
            )
            ->addArgument(
                'date',
                InputArgument::REQUIRED,
                'Date'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $date = $input->getArgument('date');


        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln('Processing');
        $months = 0;
        $points = [];
        $output->write('');
        while ($months <= 60) {
            $points[$months] = 1 + (mt_rand(0, 1000)/1000);
            $output->write($months . ':' . $points[$months] . ', ');
            $months = $months + 6;
        }
        $curvePoints = new CurvePoints($points);

        $curve = new Curve();
        $curve->type = $type;
        $curve->calculationDate = new \DateTimeImmutable($date);
        $curve->dataPoints = $curvePoints;
        $this->em->persist($curve);
        $this->em->flush();

        $output->writeln('');
        $output->writeln('Done');
    }
}
