<?php
namespace ConsoleBundle\Command;

use DateTimeImmutable;
use DOMDocument;
use DOMElement;
use DOMXPath;
use GuzzleHttp\Client;
use SecuritiesService\Data\Database\Entity\LSEAnnouncement;
use SecuritiesService\Domain\Entity\Enum\AnnouncementStatus;
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
            ->setName('lse:announcements')
            ->setDescription('Fetch and save announcements');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $feeds = [
            'http://www.londonstockexchange.com/exchange/CompanyNewsRSS.html?newsSource=RNS&headlineCode=NOT',
            'http://www.londonstockexchange.com/exchange/CompanyNewsRSS.html?newsSource=RNS&headlineCode=SEN',
        ];

        foreach ($feeds as $feed) {
            $this->processFeed($feed);
        }
        $this->output->writeln('Finished');
        return;
    }

    private function processFeed($url)
    {
        $this->output->writeln('Fetching ' . $url);

        $httpClient = new Client();
        $response = $httpClient->get($url);
        $code = $response->getStatusCode();

        if ($code != 200) {
            $this->output->writeln('Output was not successful. Status code was: ' . $code);
            return;
        }

        $body = $response->getBody();
        $rss = simplexml_load_string($body);
        if (!isset($rss->channel->item)) {
            $this->output->writeln('No news found');
            return;
        }
        foreach ($rss->channel->item as $item) {
            $title = (string) $item->title;
            $description = (string) $item->description;
            $link = (string) $item->link;
            $fetched = new DateTimeImmutable();

            $announcementRepo = $this->em->getRepository('SecuritiesService:LSEAnnouncement');

            // find an item in the database for this link
            $announcement = $announcementRepo->findOneBy(
                ['link' => $link]
            );

            if ($announcement) {
                $this->output->writeln('Announcement already fetched for this date');
                return;
            }

            $status = AnnouncementStatus::LOW;
            if (strpos($title, 'trading') !== false) {
                $status = AnnouncementStatus::NEW;
            }

            $announcement = new LSEAnnouncement();
            $announcement->status = $status;
            $announcement->title = $title;
            $announcement->description = $description;
            $announcement->link = $link;
            $announcement->dateFetched = $fetched;

            $this->output->writeln('Captured: ' . $link);
            $this->em->persist($announcement);
            $this->em->flush();
        };
    }
}
