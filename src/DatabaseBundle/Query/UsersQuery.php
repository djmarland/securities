<?php
namespace DatabaseBundle\Query;

class UsersQuery extends Query
{
    public function get()
    {
        $entity = $this->getEntity('User');
        return $this->getFromEntity($entity);
    }

    public function count()
    {
        $entity = $this->getEntity('User');
        return $this->countFromEntity($entity);
    }

    public function byEmail($email)
    {
        $this->by['email'] = $email;
        return $this;
    }



}
