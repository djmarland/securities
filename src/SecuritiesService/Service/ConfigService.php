<?php

namespace SecuritiesService\Service;


use SecuritiesService\Domain\Entity\Config;
use SecuritiesService\Domain\Exception\EntityNotFoundException;

class ConfigService extends Service
{
    const SERVICE_ENTITY = 'Config';

    public function get(): Config {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL);

        $results = $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException('No Config found');
        }

        return reset($results);
    }
}
