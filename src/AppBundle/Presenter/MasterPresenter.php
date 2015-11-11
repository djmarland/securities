<?php

namespace AppBundle\Presenter;

use AppBundle\Domain\Exception\DataNotSetException;

/**
 * Class MasterPresenter
 * The entire set of page data is passed into this presenter
 */
class MasterPresenter extends Presenter
{

    private $data = [];

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
        throw new DataNotSetException;
    }

    public function getData()
    {
        $data = array();
        ksort($this->data);
        foreach ($this->data as $key => $value) {
            $data[$key] = $value->data;
        }
        return $data;
    }

    public function getFeedData()
    {
        $data = (object) [];

        ksort($this->data);
        foreach ($this->data as $key => $value) {
            if ($value->inFeed) {
                $data->$key = $value->data;
            }
        }

        return $data;
    }
}
