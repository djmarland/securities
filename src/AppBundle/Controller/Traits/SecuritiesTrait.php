<?php

namespace AppBundle\Controller\Traits;

use AppBundle\Presenter\Molecule\Money\MoneyPresenter;
use AppBundle\Presenter\Organism\EntityNav\EntityNavPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use DateTime;
use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Service\Filter\SecuritiesFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait SecuritiesTrait
{
    private $filter = [];
    private $titleParts = [];

    public function securitiesToPresenters($securities)
    {
        $securityPresenters = [];
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }
        return $securityPresenters;
    }

    public function renderSecurities(
        Request $request,
        Entity $entity = null
    ) {
        $this->titleParts[] = 'Securities';

        if ($entity) {
            $entityType = $entity->getRoutePrefix();
            $securitiesService = $this->get('app.services.securities_by_' . $entityType);
            $securitiesService->setDomainEntity($entity);
            $this->titleParts[] = $entity->getName();
        } else {
            $securitiesService = $this->get('app.services.securities');
        }


        $filter = $this->setFilter($request);

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $total = $securitiesService->count($filter);
        $totalRaised = 0;
        $securities = [];
        if ($total) {
            $securities = $securitiesService
                ->find(
                    $perPage,
                    $currentPage,
                    $filter
                );
            $totalRaised = $securitiesService->sum($filter);
        }

        $this->toView('totalRaised', new MoneyPresenter($totalRaised, ['scale' => true]));
        $this->toView('securities', $this->securitiesToPresenters($securities));
        $this->toView('total', $total);
        $this->setTitle(implode(' - ', $this->titleParts));

        $this->setPagination(
            $total,
            $currentPage,
            $perPage
        );

        $this->toView('entityNav', new EntityNavPresenter($entity, 'securities'));
        return $this->renderTemplate('entities:securities');
    }

    private function setFilter(Request $request)
    {
        return new SecuritiesFilter(
            $this->setProductFilter($request),
            $this->setCurrencyFilter($request),
            $this->setBucketFilter($request),
            $this->setIssueDateFilter($request)
        );
    }

    private function setProductFilter(Request $request)
    {
        $this->filter['activeProduct'] = null;
        $number = $request->get('product', null);
        if ($number && (string) (int) $number !== $number) {
            throw new HttpException(404, 'Invalid ID');
        }
        $this->filter['products'] = $this->get('app.services.products')
            ->findAll();
        $product = null;
        if ($number) {
            foreach ($this->filter['products'] as $p) {
                if ($p->getNumber() == $number) {
                    $product = $p;
                }
            }
            if (!$product) {
                throw new HttpException(404, 'No such product');
            }
            $this->filter['activeProduct'] = (string) $product->getId();
            $this->titleParts[] = $product->getName();
        }
        $this->toView('filter', $this->filter);
        return $product;
    }

    private function setCurrencyFilter(Request $request)
    {
        $this->filter['activeCurrency'] = null;

        $code = $request->get('currency', null);
        if ($code && strlen($code) != 3) {
            throw new HttpException(400, 'Invalid Currency Code');
        }

        $this->filter['currencies'] = $this->get('app.services.currencies')
            ->findAll();
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
            $this->titleParts[] = $currency->getCode();
        }
        $this->toView('filter', $this->filter);
        return $currency;
    }

    private function setBucketFilter(Request $request)
    {
        $this->filter['activeBucket'] = null;

        $key = $request->get('bucket', null);

        $this->filter['buckets'] = $this->get('app.services.buckets')
            ->getAll(new DateTime()); // @todo - use application time (or re-write buckets)
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
            $this->titleParts[] = 'BUCKET'; // todo - proper name
        }
        $this->toView('filter', $this->filter);
        return $bucket;
    }

    private function setIssueDateFilter(Request $request)
    {
        $this->filter['activeIssueDate'] = null;

        $date = $request->get('issueDate', null);

        if ($date) {
            if (!preg_match('/\d{4}-\d{2}/', $date)) {
                throw new HttpException(404, 'No such bucket');
            }
            $date = $date . '-01T00:00:00Z';
            $startDate = new \DateTimeImmutable($date);
            $endDate = $startDate->add(new \DateInterval('P1M'));
            $displayEnd = $endDate->sub(new \DateInterval('P1D'));
            $this->filter['activeIssueDate'] = [
                'start' => $startDate,
                'end' => $endDate,
                'displayEnd' => $displayEnd,
            ];
            $this->titleParts[] = $startDate->format('F Y');
        }
        $this->toView('filter', $this->filter);
        return $this->filter['activeIssueDate'];
    }
}
