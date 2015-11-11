<?php

namespace DatabaseBundle\Mapper;

use AppBundle\Domain\Entity\User;
use DatabaseBundle\Entity\User as UserEntity;
use AppBundle\Domain\ValueObject\Email;
use AppBundle\Domain\ValueObject\ID;

class UserMapper extends Mapper
{
    /**
     * @param UserEntity $item
     * @return User
     */
    public function getDomainModel($item)
    {
        $id = new ID($item->getId());
        $email = new Email($item->getEmail());
        $settings = new User(
            $id,
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
            $item->getName(),
            $email,
            $item->getPasswordDigest(),
            $item->getIsActive(),
            $item->getIsAdmin()
        );

        $settings->setOrmEntity($item);
        return $settings;
    }

    public function getOrmEntity($domain)
    {
        $entity = $domain->getOrmEntity();
        if (!$entity) {
            // create a new one
            $entity = new UserEntity;
        }

        $entity->setName($domain->getName());
        $entity->setEmail((string) $domain->getEmail());
        $entity->setIsActive($domain->isActive());
        $entity->setIsAdmin($domain->isAdmin());
        $entity->setPasswordExpired($domain->passwordHasExpired());
        $entity->setPasswordDigest((string) $domain->getPasswordDigest());

        return $entity;
    }
}
