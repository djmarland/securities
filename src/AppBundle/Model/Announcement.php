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
                $issuer = trim($row->textContent);
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
            $cellContents[] = $cell->textContent;
        }

        if (!isset($cellContents[2])) {
            return [];
        }

        $amountCell = $cellContents[0];
        $detailCell = $cellContents[1];
        $isinCell = $cellContents[2];
        if (isset($cellContents[3])) {
            $isinCell = $cellContents[3];
        }

        $isin = $this->parseIsin($isinCell);
        $amount = $this->parseAmount($amountCell);

        return [
            'cellContents' => $cellContents,
            'issuer' => $issuer,
            'isin' => $isin,
            'date' => $this->date->format('Y-m-d'),
            'colspan' => count($cellContents),
            'amount' => $amount
        ];
    }

    private function parseAmount(string $amount): int
    {
        return (int) str_replace(',','',$amount);
    }

    private function parseIsin(string $cell): ISIN
    {
        preg_match(\Djmarland\ISIN\ISIN::VALIDATION_PATTERN, $cell, $isins);
        if (!isset($isins[0])) {
            var_dump($cell, $isins);
            die;
        }
        return new ISIN($isins[0]);
    }

    private function findDate()
    {
        $intro = substr($this->announcementBody, 0, strpos($this->announcementBody, '<table'));
        preg_match('/[0-9]{2}\/[0-9]{2}\/20[0-9]{2}/', $intro, $dates);
        $this->date = DateTimeImmutable::createFromFormat('d/m/Y', $dates[0], new DateTimeZone('Europe/London'));
    }
}
