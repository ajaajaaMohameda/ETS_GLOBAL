<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ODM\Document(collection: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $name;

    #[ODM\Field(type: 'string')]
    #[ODM\Index(unique: true)]
    private string $email;

    #[ODM\Field(type: 'string')]
    private string $password;

    #[ODM\Field(type: 'collection')]
    private array $roles = [];
    public function __construct(string $name, string $email, string $password)
    {
        $this->name = trim($name);
        $this->email = strtolower(trim($email));
        $this->password = $password;

        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
            return array_unique($this->roles);

    }

    public function eraseCredentials(): void
    {
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function updateProfile(string $name, string $email): void
    {
        $this->name = trim($name);
        $this->email = strtolower(trim($email));
    }

    public function changePassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function promoteToAdmin(): void
    {
        if (!in_array('ROLE_ADMIN', $this->roles, true)) {
            $this->roles[] = 'ROLE_ADMIN';
        }
    }
}