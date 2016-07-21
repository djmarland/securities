<?php


namespace SecuritiesService\Data\Database\Mapper;


use SecuritiesService\Domain\Entity\Entity;
use SecuritiesService\Domain\Entity\User;
use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Domain\ValueObject\PasswordDigest;
use SecuritiesService\Domain\ValueObject\UUID;

class UserMapper extends Mapper
{
    public function getDomainModel(array $item): Entity
    {
        $id = new UUID($item['id']);
        $name = $item['name'];
        $email = new Email($item['email']);
        $passwordDigest = new PasswordDigest($item['passwordDigest']);
        $isActive = $item['isActive'];
        $isAdmin = $item['isAdmin'];


        $security = new User(
            $id,
            $name,
            $email,
            $passwordDigest,
            false,
            $isActive,
            $isAdmin
        );
        return $security;
    }
}