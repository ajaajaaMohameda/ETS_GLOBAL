<?php

namespace App\Repository;

use App\Document\TestSession;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\DTO\Common\PaginationRequest;
readonly class TestSessionRepository
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




    public function findPaginated(PaginationRequest $pagination
    ): array
    {
            $page = $pagination->page;
    $limit = $pagination->limit;
        return $this->documentManager
            ->createQueryBuilder(TestSession::class)
            ->field('isDeleted')->equals(false)
            ->skip(($page - 1) * $limit)
            ->limit($limit)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    public function countActive(): int
    {
        return $this->documentManager
            ->createQueryBuilder(TestSession::class)
            ->field('isDeleted')->equals(false)
            ->count()
            ->getQuery()
            ->execute();
    }
}