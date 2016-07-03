<?php

namespace SecuritiesService\Service;

use SecuritiesService\Data\Database\Entity\User;
use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Domain\ValueObject\Password;

class UsersService extends Service
{
    public function createNewUser(
        Email $email,
        Password $password
    ) {
        // create a user database entity
        $user = new User();
        $user->setEmail((string) $email);
        $user->setPasswordDigest((string) $password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return;
    }
}