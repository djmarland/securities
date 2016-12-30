<?php

namespace SecuritiesService\Service;

use DateTimeInterface;

class CurvesService extends Service
{
    const SERVICE_ENTITY = 'Curve';

    public function findForDate(
        DateTimeInterface $date
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.calculationDate = :date')
            ->setParameters([
                'date' => $date,
            ]);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }
}
