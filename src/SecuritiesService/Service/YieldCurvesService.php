<?php

namespace SecuritiesService\Service;

use SecuritiesService\Domain\Entity\ParentGroup;

class YieldCurvesService extends Service
{
    const LOCAL_ENTITY = 'YieldCurve';

    public function findByParentGroupForYear(
        ParentGroup $parentGroup,
        int $year
    ): ServiceResultInterface {
        $currencyTbl = 'g';

        $qb = $this->getQueryBuilder(self::LOCAL_ENTITY);
        $qb->select(self::TBL, $currencyTbl)
            ->where('IDENTITY(' . self::TBL . '.parentGroup) = :parent_group_id')
            ->andWhere(self::TBL . '.year = :year')
            ->andWhere(self::TBL . '.type = :type')
            ->leftJoin(self::TBL . '.currency', $currencyTbl)
            ->setParameters([
                'year' => $year,
                'type' => 'PROXY',
                'parent_group_id' => $parentGroup->getId()
            ]);

        $result = $qb->getQuery()->getResult();
        return $this->getServiceResult($result);
    }
}
