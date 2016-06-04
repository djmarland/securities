<?php
namespace AppBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
class UserProvider implements UserProviderInterface
{
    private $usersService;
    public function __construct($usersService)
    {
        $this->usersService = $usersService;
    }
    public function loadUserByUsername($email)
    {
        $result = $this->usersService->findByEmail($email);
        $user = $result->getDomainModel();
        if (!$user) {
            throw new UsernameNotFoundException('No such user');
        }
        $visitor = new Visitor($user);
        return $visitor;
    }
    public function refreshUser(UserInterface $visitor)
    {
        if (!$visitor instanceof Visitor) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($visitor))
            );
        }
        return $this->loadUserByUsername((string) $visitor->getUsername());
    }
    public function supportsClass($class)
    {
        return $class === 'AppBundle\Security\Visitor';
    }
}