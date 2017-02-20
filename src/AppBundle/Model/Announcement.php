<?php

namespace AppBundle\Model;


use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMElement;
use SecuritiesService\Domain\ValueObject\ISIN;

class Announcement
{
    private $announcementBody;

    private $date;

    private $securities = [];

    public function __construct(string $announcementBody)
    {
        $this->announcementBody = $announcementBody;
        $this->parse();

    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getSecurities(): array
    {
        return $this->securities;
    }

    public function __toString(): string
    {
        return $this->announcementBody;
    }

    private function parse()
    {
        $this->findDate();

        $dom = new DOMDocument();
        $dom->loadHTML($this->announcementBody);

        $securities = [];

        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $table) {
            /** @var $table DOMElement */
            $securities = array_merge($securities, $this->handleTable($table));
        }
        $this->securities = $securities;
    }

    private function handleTable(DOMElement $table): array
    {
        $securities = [];
        $issuer = null;
        $rows = $table->getElementsByTagName('tr');
        foreach ($rows as $i => $row) {
            /** @var $row DOMElement */
            if ($i == 0) {
                $issuer = strtoupper(trim($row->textContent));
                continue;
            }
            $security = $this->handleRow($row, $issuer);
            if (!empty($security)) {
                $securities[] = $security;
            }
        }
        return $securities;
    }

    private function handleRow(DOMElement $row, string $issuer): array
    {
        $cellContents = [];
        foreach ($row->getElementsByTagName('td') as $cell) {
            /** @var $cell DOMElement */
            $cellContents[] = trim($cell->textContent);
        }

        if (!isset($cellContents[2]) || empty($cellContents[2])) {
            return [];
        }

        $amountCell = $cellContents[0];
        $detailCell = $cellContents[1];
        $isinCell = $cellContents[2];
        if (isset($cellContents[3])) {
            $isinCell = $cellContents[3];
        }

        $isin = $this->parseIsin($isinCell);
        $currency = $this->parseCurrency($amountCell, $detailCell);
        $amount = $this->parseAmount($amountCell);
        $gbpAmount = null;
        $localAmount = null;
        if ($currency === 'GBP') {
            $gbpAmount = $amount;
        } else {
            $localAmount = $amount;
        }

        return [
            'cellContents' => $cellContents,
            'issuer' => $this->cleanText($issuer),
            'isin' => (string) $isin,
            'description' => $this->cleanText($detailCell),
            'date' => $this->date->format('d/m/Y'),
            'endDate' => $this->parseEnd($detailCell),
            'coupon' => $this->parseCoupon($detailCell),
            'colspan' => count($cellContents),
            'currency' => $currency,
            'gbpAmount' => $gbpAmount,
            'localAmount' => $localAmount,
        ];
    }

    private function cleanText(string $text): string
    {
        $what   = "\\x00-\\x20";
        $text = htmlentities($text, null, 'utf-8');
        $text = str_replace("&nbsp;", "", $text);
        $text = trim( preg_replace( "/[".$what."]+/" , ' ' , $text ) , $what );
        return trim(html_entity_decode($text));
    }

    private function parseCoupon(string $details)
    {
        preg_match('/[0-9.]+%/', $details, $coupons);
        return $coupons[0] ?? null;
    }

    private function parseEnd(string $details)
    {
        preg_match('/[0-9]{2}\/[0-9]{2}\/20[0-9]{2}/', $details, $dates);
        return $dates[0] ?? null;
    }

    private function parseAmount(string $amount): float
    {
        return ((int) preg_replace("/[^0-9]/", "",$amount)) / 1000000;
    }

    private function parseCurrency(string $amount, string $detail): string
    {
        if (strpos(strtoupper($detail), '1P EACH') !== false) {
            return 'GBX';
        }
        preg_match("/[A-Z]{3}/", $amount, $matches);
        return $matches[0] ?? 'GBP';
    }

    private function parseIsin(string $cell): ISIN
    {
        preg_match(\Djmarland\ISIN\ISIN::VALIDATION_PATTERN, $cell, $isins);
        return new ISIN($isins[0]);
    }

    private function findDate()
    {
        $intro = substr($this->announcementBody, 0, strpos($this->announcementBody, '<table'));
        preg_match('/[0-9]{2}\/[0-9]{2}\/20[0-9]{2}/', $intro, $dates);
        $this->date = DateTimeImmutable::createFromFormat('d/m/Y', $dates[0], new DateTimeZone('Europe/London'));
    }
}
