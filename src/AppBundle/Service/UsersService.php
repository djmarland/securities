<?php

namespace AppBundle\Service;

use AppBundle\Domain\Entity\User;
use AppBundle\Domain\ValueObject\ID;
use AppBundle\Domain\ValueObject\Key;

class UsersService extends Service
{

    /**
     * @param User $user
     * @return User
     */
    public function createNewUser(
        User $user
    ) {
        $id = $this->getQueryFactory()
            ->createUsersQuery()
            ->insert($user);
        $user->setId(new ID($id));
        return $user;
    }

    /**
     * @param $limit
     * @param $page
     * @return ServiceResult
     */
    public function findAndCountLatest(
        $limit,
        $page = 1
    ) {

        // count them first (cheaper if zero)
        $count = $this->countAll();
        if ($count == 0) {
            return new ServiceResultEmpty();
        }

        // find the latest
        $result = $this->findLatest($limit, $page);
        $result->setTotal($count);
        return $result;
    }

    public function countAll()
    {
        return $this->getQueryFactory()
            ->createUsersQuery()
            ->count();
    }

    public function findLatest(
        $limit,
        $page = 1
    ) {
        $users = $this->getQueryFactory()
            ->createUsersQuery()
            ->sortByCreationDate('DESC')
            ->paginate($limit, $page)
            ->get();

        return new ServiceResult($users);
    }

    /**
     * @param ID $id
     * @return \AppBundle\Domain\Entity\User|null
     */
    public function findById(ID $id)
    {
        $result = $this->getQueryFactory()
            ->createUsersQuery()
            ->byId((string) $id)
            ->get();
        if ($result) {
            return new ServiceResult($result);
        }
        return new ServiceResultEmpty();
    }

    public function findByKey(Key $key)
    {
        $id = $key->getId();
        return $this->findById($id);
    }

    /**
     * @param $email
     * @return \AppBundle\Domain\Entity\User|null
     */
    public function findByEmail($email)
    {
        $result = $this->getQueryFactory()
            ->createUsersQuery()
            ->byEmail((string) $email)
            ->get();
        if ($result) {
            return new ServiceResult($result);
        }
        return new ServiceResultEmpty();
    }

    public function countByEmail($email)
    {
        $result = $this->getQueryFactory()
            ->createUsersQuery()
            ->byEmail((string) $email)
            ->count();
        return $result;
    }
}
