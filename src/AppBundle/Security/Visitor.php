<?php
namespace AppBundle\Security;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
class Visitor implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * @var User
     */
    private $userEntity;
    private $username;
    private $password;
    private $id;
    public function __construct(
        User $userEntity
    ) {
        $this->userEntity = $userEntity;
        $this->id = (string) $userEntity->getId();
        $this->username = (string) $userEntity->getEmail();
        $this->password = (string) $userEntity->getPasswordDigest();
    }
    public function getUser()
    {
        return $this->userEntity;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function passwordMatches($match)
    {
        return $this->getUser()->passwordMatches($match);
    }
    public function getSalt()
    {
        return null; // no salt
    }
    public function getRoles()
    {
        return array('ROLE_USER');
    }
    public function eraseCredentials()
    {
        $this->userEntity = null;
    }
    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password
        ));
    }
    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->passwordDigest
            ) = unserialize($serialized);
    }
    public function isEqualTo(UserInterface $user)
    {
        return (
            (string) $this->getId() == (string) $user->getId() &&
            (string) $this->getUsername() == (string) $user->getUsername() &&
            (string) $this->getPassword() == (string) $user->getPassword()
        );
    }
}