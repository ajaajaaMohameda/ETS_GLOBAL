<?php

namespace App\Repository;

use App\Document\TestSession;
use Doctrine\ODM\MongoDB\DocumentManager;

final readonly class TestSessionRepository
{
    public function __construct(
        private DocumentManager $documentManager,
    ) {
    }

    public function save(TestSession $session): void
    {
        $this->documentManager->persist($session);
        $this->documentManager->flush();
    }

    public function findActiveById(
        string $id
    ): ?TestSession {
        return $this->documentManager
            ->getRepository(TestSession::class)
            ->findOneBy([
                'id' => $id,
                'isDeleted' => false,
            ]);
    }
}