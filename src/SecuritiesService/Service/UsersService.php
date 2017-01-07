<?php

namespace SecuritiesService\Service;

use DateTimeImmutable;
use SecuritiesService\Data\Database\Entity\User as DbUser;
use SecuritiesService\Domain\Entity\User;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\InvalidCredentialsException;
use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Domain\ValueObject\Password;
use SecuritiesService\Domain\ValueObject\PasswordDigest;
use SecuritiesService\Domain\ValueObject\ResetToken;

class UsersService extends Service
{
    const ENTITY = 'User';

    public function countAll(): int
    {
        $qb = $this->getQueryBuilder(self::ENTITY);
        $qb->select('count(' . self::TBL . '.id)');
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findByEmail(Email $email): User
    {
        $qb = $this->getQueryBuilder(self::ENTITY);
        $qb->where(self::TBL . '.email = :email')
            ->setParameter('email', (string) $email);

        $results = $this->getDomainFromQuery($qb, self::ENTITY);
        if (empty($results)) {
            throw new EntityNotFoundException();
        }
        return reset($results);
    }

    public function emailExists(Email $email): bool
    {
        try {
            $this->findByEmail($email);
            return true;
        } catch (EntityNotFoundException $e) {
            return false;
        }
    }

    public function createNewUser(
        Email $email,
        Password $password
    ): User {
        // create a user database entity
        $user = new DbUser();
        $user->email = (string) $email;
        $user->passwordDigest = (string) $password;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // re-fetch the item just created
        return $this->findByEmail($email);
    }

    public function generateAndSavePasswordToken(
        User $user,
        \DateTimeImmutable $now
    ): ResetToken {
        $expiry = $now->add(
            new \DateInterval('P7D')
        );

        // generate a new token
        $token = new ResetToken();

        $userDb = $this->getDbEntityFromDomain($user);

        $userDb->resetTokenUsername = $token->getUsername();
        $userDb->resetTokenDigest = (string) $token->getDigest();
        $userDb->resetTokenExpiry = $expiry;

        // save it
        $this->entityManager->persist($userDb);
        $this->entityManager->flush();

        // return the token
        return $token;
    }

    public function resetPassword(
        ResetToken $token,
        Password $newPassword,
        DateTimeImmutable $now
    ) {
        // get the user DB entity by the token
        $qb = $this->getQueryBuilder(self::ENTITY);
        $qb->where(self::TBL . '.resetTokenUsername = :token')
            ->setParameter('token', $token->getUsername());

        /** @var DbUser $userDb */
        $userDb = $qb->getQuery()->getOneOrNullResult();

        // ensure the user existed
        if (!$userDb) {
            throw new InvalidCredentialsException('Token incorrect');
        }

        // ensure the token expiry hasn't passed
        if ($userDb->resetTokenExpiry < $now) {
            throw new InvalidCredentialsException('Token expired');
        }

        // ensure the reset password is correct
        $digest = new PasswordDigest($userDb->resetTokenDigest);
        if (!$digest->matches($token->getPassword())) {
            throw new InvalidCredentialsException('Token invalid');
        }

        // update the password
        $userDb->passwordDigest = (string) $newPassword;

        // remove the reset token details
        $userDb->resetTokenUsername = null;
        $userDb->resetTokenDigest = null;
        $userDb->resetTokenExpiry = null;

        // save it
        $this->entityManager->persist($userDb);
        $this->entityManager->flush();

        // success
        return true;
    }

    public function updateEmailAddress(
        User $user,
        Email $newEmail
    ): bool {
        $userDb = $this->getDbEntityFromDomain($user);
        $userDb->email = (string) $newEmail;

        // save it
        $this->entityManager->persist($userDb);
        $this->entityManager->flush();

        return true;
    }

    public function updatePassword(
        User $user,
        Password $password
    ): bool {
        $userDb = $this->getDbEntityFromDomain($user);
        $userDb->passwordDigest = (string) $password;

        // save it
        $this->entityManager->persist($userDb);
        $this->entityManager->flush();

        return true;
    }

    private function getDbEntityFromDomain(User $user): DbUser
    {
        // get the user DB entity
        $qb = $this->getQueryBuilder(self::ENTITY);
        $qb->where(self::TBL . '.id = :id')
            ->setParameter('id', $user->getId()->getBinary());

        /** @var DbUser $userDb */
        $userDb = $qb->getQuery()->getOneOrNullResult();
        if (!$userDb) {
            throw new \InvalidArgumentException(
                'Tried to update a user that could not be found'
            );
        }
        return $userDb;
    }
}
