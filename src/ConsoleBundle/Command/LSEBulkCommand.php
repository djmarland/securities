<?php
namespace ConsoleBundle\Command;

use DateTimeImmutable;
use DOMDocument;
use DOMElement;
use DOMXPath;
use GuzzleHttp\Client;
use SecuritiesService\Data\Database\Entity\LSEAnnouncement;
use SecuritiesService\Domain\Entity\Enum\AnnouncementStatus;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LSEAnnouncementsCommand extends Command
{
    protected $em;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('lse:bulk')
            ->setDescription('Fetch and save multipleannouncements')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to input file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $name = $input->getArgument('file');
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $data = $this->csvToArray($name);
        if (!$data) {
            $output->writeln('No such file');
            return;
        }

        foreach ($data as $item) {
            $this->createAnnouncement($item['URL'], $item['Issue date']);
        }
        $this->output->writeln('Finished');
        return;
    }

    private function createAnnouncement($url, $date)
    {
        $title = 'Admission to trading (manual upload)';
        $description = 'Admission to trading (manual upload)';
        $link = trim($url);
        $fetched = new DateTimeImmutable(trim($date));

        $announcementRepo = $this->em->getRepository('SecuritiesService:LSEAnnouncement');

        // find an item in the database for this link
        $announcement = $announcementRepo->findOneBy(
            ['link' => $link]
        );

        if ($announcement) {
            $this->output->writeln('Announcement already fetched for this date');
            return;
        }

        $announcement = new LSEAnnouncement();
        $announcement->status = AnnouncementStatus::NEW;
        $announcement->title = $title;
        $announcement->description = $description;
        $announcement->link = $link;
        $announcement->dateFetched = $fetched;

        $this->output->writeln('Captured: ' . $link);
        $this->em->persist($announcement);
        $this->em->flush();
    }
}
