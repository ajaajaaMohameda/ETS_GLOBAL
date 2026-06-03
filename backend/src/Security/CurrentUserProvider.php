<?php

namespace App\Security;

use App\Document\User;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CurrentUserProvider
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function getUser(): User
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $user;
    }
}