<?php

namespace App\Repository;

use App\Document\Reservation;
use App\Document\TestSession;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;

readonly class ReservationRepository
{
    public function __construct(
        private DocumentManager $documentManager
    ) {
    }

    public function save(
        Reservation $reservation
    ): void {
        $this->documentManager->persist($reservation);
        $this->documentManager->flush();
    }

    public function findById(
        string $id
    ): ?Reservation {
        return $this->documentManager
            ->getRepository(Reservation::class)
            ->find($id);
    }

    public function findByUser(
        User $user
    ): array {
        return $this->documentManager
            ->createQueryBuilder(Reservation::class)
            ->field('user')->references($user)
            ->field('isCancelled')->equals(false)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    public function exists(
        User $user,
        TestSession $session
    ): bool {

        $count = $this->documentManager
            ->createQueryBuilder(Reservation::class)
            ->field('user')->references($user)
            ->field('session')->references($session)
            ->field('isCancelled')->equals(false)
            ->count()
            ->getQuery()
            ->execute();

        return $count > 0;
    }
}