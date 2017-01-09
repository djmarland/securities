<?php
namespace AppBundle\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Domain\ValueObject\Password;
use SecuritiesService\Service\UsersService;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
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

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): Visitor
    {
        $email = new Email($response->getEmail());
        try {
            $user = $this->usersService->findByEmail($email);
        } catch (EntityNotFoundException $e) {
            // User was not found. Let's make them a new account (with a suitably nonsense password)
            $user = $this->usersService->createNewUser($email, new Password(sha1(mt_rand() . time())));
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
