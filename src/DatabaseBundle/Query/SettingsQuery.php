<?php
namespace DatabaseBundle\Query;

class SettingsQuery extends Query
{
    public function get()
    {
        $entity = $this->getEntity('Settings')->findOneById(1);
        return $this->getDomainModels($entity);
    }
}
