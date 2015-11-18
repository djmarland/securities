<?php

namespace AppBundle\Service;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\ISIN;

class SecuritiesService extends Service
{
    public function findAndCountAll(
        int $limit,
        int $page = 1
    ): ServiceResultInterface {

        // count them first (cheaper if zero)
        $count = $this->countAll();
        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findAll($limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countAll(): int
    {
        return $this->getQueryFactory()
            ->createSecuritiesQuery()
            ->count();
    }

    public function findAll(
        int $limit,
        int $page = 1
    ): ServiceResultInterface {
        $securities = $this->getQueryFactory()
            ->createSecuritiesQuery()
            ->sortByIsin('ASC')
            ->paginate($limit, $page)
            ->get();

        if ($securities) {
            return new ServiceResult($securities);
        }
        return new ServiceResultEmpty();
    }

    public function searchAndCount(
        string $query,
        int $limit,
        int $page = 1
    ): ServiceResultInterface {
        $factory = $this->getQueryFactory();

        $count = $factory
            ->createSecuritiesQuery()
            ->countSearch($query);

        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find them
        $result = $this->search($query, $limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function search(
        string $query,
        int $limit,
        int $page = 1
    ): ServiceResultInterface {
        $securities = $this->getQueryFactory()
            ->createSecuritiesQuery()
            ->paginate($limit, $page)
            ->search($query);

        if ($securities) {
            return new ServiceResult($securities);
        }
        return new ServiceResultEmpty();
    }

    public function findById(ID $id): ServiceResultInterface
    {
        $result = $this->getQueryFactory()
            ->createSecuritiesQuery()
            ->byId((string) $id)
            ->get();
        if ($result) {
            return new ServiceResult($result);
        }
        return new ServiceResultEmpty();
    }

    public function findByIsin(ISIN $isin): ServiceResultInterface
    {
        $result = $this->getQueryFactory()
            ->createSecuritiesQuery()
            ->byIsin((string) $isin)
            ->get();
        if ($result) {
            return new ServiceResult($result);
        }
        return new ServiceResultEmpty();
    }
}
