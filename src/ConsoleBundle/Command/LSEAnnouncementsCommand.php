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
            'http://www.londonstockexchange.com/exchange/CompanyNewsRSS.html?newsSource=RNS&headlineCode=SEN'
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
        foreach($rss->channel->item as $item) {
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
                $this->output->writeln('Rates already fetched for this date');
                return;
            }

            $announcement = new LSEAnnouncement();
            $announcement->setStatus(AnnouncementStatus::NEW);
            $announcement->setTitle($title);
            $announcement->setDescription($description);
            $announcement->setLink($link);
            $announcement->setDateFetched($fetched);

            $this->output->writeln('Captured: ' . $link);
            $this->em->persist($announcement);
            $this->em->flush();
        };
    }

    private function processUrl($url)
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

        // only want the announcement stuff

        $startString = '<!-- Begin announcement content -->';
        $endPoint = '<!-- End announcement content -->';

        $startsAt = strpos($body, $startString) + strlen($startString);
        $endsAt = strpos($body, $endPoint, $startsAt);
        $body = trim(substr($body, $startsAt, $endsAt - $startsAt));

        file_put_contents(__DIR__ . '../../../saved.html', $body);
        $this->output->writeln('Fetched. Total size: ' . strlen($body) . ' characters');

        $this->output->writeln('Parsing DOM');
        $dom = new DOMDocument();
        $dom->loadHTML($body);
        $xpath = new DOMXPath($dom);
        $tables = $xpath->query('//table[@class="ad"]');

        $this->output->writeln($tables->length . ' tables found');

        $issuers = [];

        foreach ($tables as $table) {
            /** @var $table DOMElement $doc */
            $rows = $table->getElementsByTagName('tr');
            $count = $rows->length;

            $issuer = trim($rows->item(0)->textContent);
            $issuer = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $issuer);
            $issuer = urldecode($issuer);
            $this->output->writeln($issuer);

//            $this->output->writeln($tables->length . ' tables found');


//            $doc = new DOMDocument();
//            $cloned = $table->cloneNode(true);
//            $doc->appendChild($doc->importNode($cloned, true));
//            $tableXPath = new DOMXPath($doc);
//            $cells = $tableXPath->query('//td');
//
//
//
//            foreach ($cells as $cell) {
//                /** @var $cell DOMElement */
//                $class = $cell->getAttribute('class');
//                switch ($class) {
//                    case 'u':
//
//                }
//            }
        }

    }

//    private function process($date, OutputInterface $output)
//    {
//        $formattedDate = $date->format('Y-m-d');
//        $output->writeln('Fetching for ' . $formattedDate);
//
//        $this->em = $this->getContainer()->get('doctrine')->getManager();
//        $rateRepo = $this->em->getRepository('SecuritiesService:ExchangeRate');
//
//        // find an item in the database for this date
//        $rate = $rateRepo->findOneBy(
//            ['date' => $date]
//        );
//
//        if ($rate) {
//            $output->writeln('Rates already fetched for this date');
//            return;
//        }
//        $output->writeln('Not yet fetched. Fetching...');
//
//        $fixerioExchange = new Exchange();
//        $fixerioExchange
//            ->base(\Fadion\Fixerio\Currency::USD)
//            ->historical($formattedDate);
//
//        $result = $fixerioExchange->getResult();
//        $resultDate = $result->getDate()->format('Y-m-d');
//
//        if ($resultDate != $formattedDate) {
//            $output->writeln('Date of feed is ' . $resultDate . '. Expected ' . $formattedDate . '. Stopping');
//            return;
//        }
//
//        $rates = $result->getRates();
//
//        foreach ($rates as $code => $rate) {
//            $currency = $this->getCurrency($code);
//            $exchangeRate = new ExchangeRate();
//            $exchangeRate->setCurrency($currency);
//            $exchangeRate->setDate($date);
//            $exchangeRate->setRate($rate);
//            $this->em->persist($exchangeRate);
//            $this->em->flush();
//            $output->writeln('Saved ' . $currency->getCode() . ' at ' . $rate);
//        }
//    }
//
//
//    private function getCurrency($code)
//    {
//        $repo = $this->em->getRepository('SecuritiesService:Currency');
//        $currency = $repo->findOneBy(
//            ['code' => $code]
//        );
//        if (!$currency) {
//            $currency = new Currency();
//        }
//        $currency->setCode($code);
//        $this->em->persist($currency);
//        $this->em->flush();
//        return $currency;
//    }
}
