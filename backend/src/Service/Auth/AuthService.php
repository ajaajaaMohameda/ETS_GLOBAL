<?php

namespace App\Service\Auth;

use App\Document\User;
use App\DTO\Auth\RegisterUserRequest;
use App\Exception\EmailAlreadyUsedException;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class AuthService
{
    public function __construct(
        private DocumentManager $documentManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {
    }

    public function register(RegisterUserRequest $request): User
    {
        if ($this->userRepository->findOneByEmail($request->email) !== null) {
            throw new EmailAlreadyUsedException();
        }

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

        $this->userRepository->save($user);

        return $user;
    }
}