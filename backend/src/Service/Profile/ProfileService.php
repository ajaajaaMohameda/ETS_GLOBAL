<?php

namespace App\Service\Profile;

use App\Document\User;
use App\Security\CurrentUserProvider;

final readonly class ProfileService
{
    public function __construct(
        private CurrentUserProvider $currentUserProvider,
    ) {
    }

    public function getCurrentUser(): User
    {
        return $this->currentUserProvider->getUser();
    }
}