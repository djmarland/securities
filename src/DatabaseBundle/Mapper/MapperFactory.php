<?php

namespace DatabaseBundle\Mapper;

use DatabaseBundle\Entity\Settings as SettingsOrm;
use DatabaseBundle\Entity\User as UserOrm;
use AppBundle\Domain\Entity\Settings;
use AppBundle\Domain\Entity\User;

/**
 * Factory to create mappers as needed
 */
class MapperFactory
{

    public function __construct()
    {
    }

    public function getMapper($item)
    {
        // decide which mapper is needed based on the incoming data
        // this needs to be able to recognise data, and sub data achieved through joins
        if ($item instanceof SettingsOrm ||
            $item instanceof Settings) {
            return $this->createSettings();
        }
        if ($item instanceof UserOrm ||
            $item instanceof User) {
            return $this->createUser();
        }


        $type = 'customer'; // hack. of course they're not all customers

        $domain = null;

        if ($type == 'customer') {
            return $this->createCustomer();
        }
    }

    /**
     * Shortcut, if you only have one item
     * @param $item
     * @return Settings
     */
    public function getDomainModel($item)
    {
        $mapper = $this->getMapper($item);
        return $mapper->getDomainModel($item);
    }

    public function createCustomer()
    {
        $customerMapper = new CustomerMapper($this);
        return $customerMapper;
    }

    public function createSettings()
    {
        $settingsMapper = new SettingsMapper($this);
        return $settingsMapper;
    }

    public function createUser()
    {
        $userMapper = new UserMapper($this);
        return $userMapper;
    }
}
