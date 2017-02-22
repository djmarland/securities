<?php

namespace AppBundle\Service;

use AppBundle\Model\Announcement;
use GuzzleHttp\Client;

class AnnouncementService
{
    private $storagePath;

    public function __construct(string $storagePath)
    {
        $this->storagePath = $storagePath;
    }

    public function getSource(string $url): Announcement
    {
        $source = $this->getUrl($url);
        return new Announcement($source);
    }

    private function getUrl(string $url)
    {
        $cachePath = $this->storagePath . '/lse-announcement-' . md5($url);
        if (file_exists($cachePath)) {
            return file_get_contents($cachePath);
        }

        $client = new Client();
        $response = $client->get($url);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Data from LSE failed. Try again');
        }
        $body = $response->getBody();

        // only want the announcement stuff
        $body = $this->fetchBetween($body, '<!-- Begin announcement content -->', '<!-- End announcement content -->');
        $body = $this->fetchBetween($body, '<body ', '</body>', true);

        file_put_contents($cachePath, $body);
        return $body;
    }

    private function fetchBetween($string, $start, $end, $include = false)
    {
        $startsAt = strpos($string, $start) + strlen($start);
        $endsAt = strpos($string, $end, $startsAt);
        $string = trim(substr($string, $startsAt, $endsAt - $startsAt));
        if ($include) {
            $string = $start . $string . $end;
        }
        return $string;
    }
}
