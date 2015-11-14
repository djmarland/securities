<?php

namespace AppBundle\Service;

use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\ISIN;

class SecuritiesService extends Service
{
    public function findAndCountLatest(
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

        return new ServiceResult($securities);
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
