<?php

namespace AppBundle\Presenter;


/**
 * Class MasterPresenter
 * The entire set of page data is passed into this presenter
 */
class MasterPresenter extends Presenter
{

    private $data = [];

    private $meta = [
        'title' => '',
        'fullTitle' => '',
        'siteTitle' => ''
    ];

    private $title = '';

    private $appConfig;

    public function __construct($appConfig)
    {
        parent::__construct();
        $this->appConfig = $appConfig;
        $this->meta['title'] = $this->appConfig['title'];
        $this->meta['fullTitle'] = $this->appConfig['title'];
        $this->meta['siteTitle'] = $this->appConfig['title'];
    }

    /**
     * @param $key string
     * @param $value mixed
     * @param $allowedInFeed boolean whether this data should be public in feeds
     */
    public function set(
        $key,
        $value,
        $allowedInFeed = true
    ) {
        $this->data[$key] = (object) [
            'data' => $value,
            'inFeed' => $allowedInFeed
        ];
    }

    public function get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key]->data;
        }
        throw new \Exception; // todo - use a custom one
    }

    public function getData()
    {
        // meta always present
        $data = [
            'meta' => $this->meta
        ];
        ksort($this->data);
        foreach ($this->data as $key => $value) {
            $data[$key] = $value->data;
        }
        return $data;
    }

    public function getFeedData()
    {
        $data = (object) [
            'meta' => (object) $this->meta
        ];
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
        $this->meta['fullTitle'] = $title . ' - ' . $this->appConfig['title'];
    }
}
