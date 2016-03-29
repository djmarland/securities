<?php

namespace AppBundle\Presenter;

/**
 * Class MasterPresenter
 * The entire set of page data is passed into this presenter
 */
class MasterPresenter extends Presenter
{

    private $data = [];
    private $appConfig;
    private $meta = [
        'title' => '',
        'fullTitle' => '',
        'siteTitle' => '',
        'environment' => 'prod',
        'canonicalUrl' => '',
    ];

    public function __construct($appConfig, $env = null)
    {
        parent::__construct();
        if ($env) {
            $this->meta['environment'] = $env;
        }
        $this->updateConfig($appConfig);
    }

    public function updateConfig($config)
    {
        $this->appConfig = $config;
        $this->meta['title'] = $this->appConfig->getSiteTitle();
        $this->meta['fullTitle'] = $this->appConfig->getSiteTitle();
        $this->meta['siteTitle'] = $this->appConfig->getSiteTitle();
        $requestPath = $_SERVER['REQUEST_URI'] ?? '/';
        $this->meta['canonicalUrl'] = $this->appConfig->getSiteHostName() . $requestPath;
    }

    public function set(
        string $key,
        $value,
        bool $allowedInFeed = null
    ) {
        if (is_null($allowedInFeed)) {
            $allowedInFeed = false;
            if (isset($this->data[$key])) {
                $allowedInFeed = $this->data[$key]->inFeed;
            }
        }

        $this->data[$key] = (object) [
            'data' => $value,
            'inFeed' => $allowedInFeed,
        ];
    }

    public function get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key]->data;
        }
        throw new \Exception(); // todo - use a custom one
    }

    public function getData()
    {
        // meta and config always present
        $data = [
            'meta' => $this->meta,
            'appConfig' => $this->appConfig,
        ];
        ksort($this->data);
        foreach ($this->data as $key => $value) {
            $data[$key] = $value->data;
        }
        return $data;
    }

    public function getFeedData()
    {
        $data = (object) ['meta' => (object) $this->meta];
        ksort($this->data);
        foreach ($this->data as $key => $value) {
            if ($value->inFeed) {
                $data->$key = $value->data;
            }
        }

        return $data;
    }

    public function setTitle(string $title)
    {
        $this->meta['title'] = $title;
        $this->meta['fullTitle'] = $title . ' - ' . $this->appConfig->getSiteTitle();
    }

    // use this when you don't want the auto appended suffix
    public function setFullTitle(string $title)
    {
        $this->meta['fullTitle'] = $title;
    }
}
