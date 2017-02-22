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

        foreach ($tables as $table) {
            /** @var $table DOMElement $doc */
            $rows = $table->getElementsByTagName('tr');
            $count = $rows->length;

            $issuer = trim($rows->item(0)->textContent);
            $issuer = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $issuer);
            $issuer = urldecode($issuer);
            $this->output->writeln($issuer);
        }
    }
}
