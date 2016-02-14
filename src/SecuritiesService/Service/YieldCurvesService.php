<?php

namespace SecuritiesService\Service;

use Doctrine\ORM\QueryBuilder;
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
                'parent_group_id' => (string) $parentGroup->getId()
            ]);

        return $this->getServiceResult($qb);
    }

    protected function getServiceResult(QueryBuilder $qb, $type = 'YieldCurve')
    {
        return parent::getServiceResult($qb, $type);
    }
}
