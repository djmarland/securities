<?php

namespace DataService\Service;

use GuzzleHttp\ClientInterface;

abstract class Service
{

    protected $httpClient;
    protected $apiKey;
    protected $creatorEmail;

    public function __construct(
        ClientInterface $httpClient,
        $apiKey,
        $creatorEmail
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->creatorEmail = $creatorEmail;
    }
}
