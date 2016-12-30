<?php
namespace AppBundle\Security;

use SecuritiesService\Domain\Entity\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Visitor implements UserInterface, \Serializable, EquatableInterface
{
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

    public function getUser(): User
    {
        return $this->userEntity;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDisplayName(): string
    {
        return $this->getUsername();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function passwordMatches(string $match): bool
    {
        return $this->getUser()->passwordMatches($match);
    }

    public function getSalt(): ?string
    {
        return null; // no salt
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

//        if ($this->getUser()->getLevel() == User::BRONZE) {
//            $roles[] = 'ROLE_BRONZE';
//        }

        if ($this->getUser()->isAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        }

        return $roles;
    }

    public function eraseCredentials(): void
    {
        $this->userEntity = null;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized): void
    {
        list (
            $this->id,
            $this->username,
            $this->passwordDigest
            ) = unserialize($serialized);
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return (
            (string) $this->getId() == (string) $user->getId() &&
            (string) $this->getUsername() == (string) $user->getUsername() &&
            (string) $this->getPassword() == (string) $user->getPassword()
        );
    }
}
