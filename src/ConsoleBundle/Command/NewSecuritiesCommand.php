<?php
namespace ConsoleBundle\Command;

use DateTimeImmutable;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Fadion\Fixerio\Exchange;
use GuzzleHttp\Client;
use SecuritiesService\Data\Database\Entity\Currency;
use SecuritiesService\Data\Database\Entity\ExchangeRate;
use SecuritiesService\Data\Database\Entity\YieldCurve;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewSecuritiesCommand extends Command
{
    protected $em;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('securities:stage')
            ->setDescription('Find new securities for staging area');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $url = 'http://www.londonstockexchange.com/exchange/news/market-news/market-news-detail/other/12713997.html';
        $url = 'http://www.londonstockexchange.com/exchange/news/market-news/market-news-detail/other/12924053.html';
        $this->processUrl($url);

//        $fromDate = $input->getArgument('dateFrom');
//        if (!$fromDate) {
//            $this->process(new DateTimeImmutable(), $output);
//            return;
//        }
//
//        $fromDate = new DateTimeImmutable($fromDate);
//        $toDate = $input->getArgument('dateTo');
//        if ($toDate) {
//            $toDate = new DateTimeImmutable($toDate);
//        } else {
//            $toDate = $fromDate;
//        }
//
//        $oneDay = new \DateInterval('P1D');
//
//        while ($fromDate <= $toDate) {
//            $this->process($fromDate, $output);
//            $fromDate = $fromDate->add($oneDay);
//        }
        $this->output->writeln('Finished');
        return;
    }

    private function processUrl(string $url)
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
