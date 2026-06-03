<?php

namespace App\Repository;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;

final readonly class UserRepository
{
    public function __construct(
        private DocumentManager $documentManager,
    ) {
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->documentManager
            ->getRepository(User::class)
            ->findOneBy([
                'email' => strtolower(trim($email)),
            ]);
    }
}