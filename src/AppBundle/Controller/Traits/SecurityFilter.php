<?php

namespace AppBundle\Controller\Traits;

use DateTime;
use InvalidArgumentException;
use SecuritiesService\Domain\ValueObject\ID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait SecurityFilter
{
    private $filter = [];

    private function getAndCheckId($request, $name)
    {
        $queryId = $request->get($name, null);
        if (!$queryId) {
            return null;
        }

        try {
            $id = new ID($queryId);
            return $id;
        } catch (InvalidArgumentException $e) {
            throw new HttpException(400, 'Invalid Product ID');
        }
    }

    public function setProductFilter(Request $request)
    {
        $this->filter['activeProduct'] = null;
        $id = $this->getAndCheckId($request, 'product');
        $this->filter['products'] = $this->get('app.services.products')
            ->findAll()->getDomainModels();
        $product = null;
        if ($id) {
            foreach ($this->filter['products'] as $p) {
                if ($p->getId() == $id) {
                    $product = $p;
                }
            }
            if (!$product) {
                throw new HttpException(404, 'No such product');
            }
            $this->filter['activeProduct'] = (string)$product->getId();
        }
        $this->toView('filter', $this->filter);
        return $product;
    }

    public function setCurrencyFilter(Request $request)
    {
        $this->filter['activeCurrency'] = null;

        $code = $request->get('currency', null);
        if ($code && strlen($code) != 3) {
            throw new HttpException(400, 'Invalid Currency Code');
        }

        $this->filter['currencies'] = $this->get('app.services.currencies')
            ->findAll()->getDomainModels();
        $currency = null;
        if ($code) {
            foreach ($this->filter['currencies'] as $c) {
                if ($c->getCode() == $code) {
                    $currency = $c;
                }
            }
            if (!$currency) {
                throw new HttpException(404, 'No such currency');
            }
            $this->filter['activeCurrency'] = (string) $currency->getCode();
        }
        $this->toView('filter', $this->filter);
        return $currency;
    }

    public function setBucketFilter(Request $request)
    {
        $this->filter['activeBucket'] = null;

        $key = $request->get('bucket', null);

        $this->filter['buckets'] = $this->get('app.services.buckets')
            ->getAll(new DateTime)->getDomainModels(); // @todo - use application time (or re-write buckets)
        $bucket = null;
        if ($key) {
            foreach ($this->filter['buckets'] as $c) {
                if ($c->getKey() == $key) {
                    $bucket = $c;
                }
            }
            if (!$bucket) {
                throw new HttpException(404, 'No such bucket');
            }
            $this->filter['activeBucket'] = (string) $bucket->getKey();
        }
        $this->toView('filter', $this->filter);
        return $bucket;
    }
}
