<?php

namespace App\Service\Profile;

use App\DTO\Profile\UpdateProfileRequest;
use App\Document\User;
use App\Exception\EmailAlreadyUsedException;
use App\Repository\UserRepository;
use App\Security\CurrentUserProvider;

final readonly class ProfileService
{
    public function __construct(
        private CurrentUserProvider $currentUserProvider,
        private UserRepository $userRepository,
    ) {
    }

    public function getCurrentUser(): User
    {
        return $this->currentUserProvider->getUser();
    }

    public function updateProfile(UpdateProfileRequest $request): User
    {
        $user = $this->currentUserProvider->getUser();

        $existingUser = $this->userRepository
            ->findOneByEmail($request->email);

        if (
            $existingUser !== null &&
            $existingUser->getId() !== $user->getId()
        ) {
            throw new EmailAlreadyUsedException();
        }

        $user->updateProfile(
            $request->name,
            $request->email
        );

        $this->userRepository->save($user);

        return $user;
    }
}