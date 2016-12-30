<?php
namespace AppBundle\Security;

use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Service\UsersService;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserProvider implements UserProviderInterface
{
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    public function loadUserByUsername($email): Visitor
    {
        try {
            $user = $this->usersService->findByEmail(new Email($email));
        } catch (EntityNotFoundException $e) {
            throw new UsernameNotFoundException('No such user');
        }
        $visitor = new Visitor($user);
        return $visitor;
    }

    public function refreshUser(UserInterface $visitor): Visitor
    {
        if (!$visitor instanceof Visitor) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($visitor))
            );
        }
        return $this->loadUserByUsername((string) $visitor->getUsername());
    }

    public function supportsClass($class): bool
    {
        return $class === 'AppBundle\Security\Visitor';
    }
}
