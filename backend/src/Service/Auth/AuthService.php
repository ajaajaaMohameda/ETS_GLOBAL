<?php

namespace App\Service\Auth;

use App\Document\User;
use App\DTO\Auth\RegisterUserRequest;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class AuthService
{
    public function __construct(
        private DocumentManager $documentManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function register(RegisterUserRequest $request): User
    {
        $user = new User(
            $request->name,
            $request->email,
            ''
        );

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $request->password
        );

        $user->changePassword($hashedPassword);

        $this->documentManager->persist($user);
        $this->documentManager->flush();

        return $user;
    }
}