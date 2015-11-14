<?php
namespace DatabaseBundle\Query;

class SecuritiesQuery extends Query
{
    const ENTITY_NAME = 'Security';

    public function get(): array
    {
        $entity = $this->getEntity(self::ENTITY_NAME);
        return $this->getFromEntity($entity);
    }

    public function count(): int
    {
        $entity = $this->getEntity(self::ENTITY_NAME);
        return $this->countFromEntity($entity);
    }

    public function byIsin(string $isin): Query
    {
        $this->by['isin'] = $isin;
        return $this;
    }

    public function sortByIsin(string $direction = 'DESC'): Query
    {
        $this->sort = ['isin' => $direction];
        return $this;
    }
}
