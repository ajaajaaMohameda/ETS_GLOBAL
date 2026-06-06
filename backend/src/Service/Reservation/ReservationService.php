<?php

namespace App\Service\Reservation;

use App\Document\Reservation;
use App\Document\User;
use App\DTO\Reservation\CreateReservationRequest;
use App\Exception\NoAvailableSeatsException;
use App\Exception\ReservationAlreadyExistsException;
use App\Exception\ReservationNotFoundException;
use App\Exception\SessionNotFoundException;
use App\Exception\UnauthorizedReservationAccessException;
use App\Repository\ReservationRepository;
use App\Repository\TestSessionRepository;
use App\Security\CurrentUserProvider;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Mapper\ReservationMapper;
use App\DTO\Common\PaginationRequest;
use App\DTO\Common\PaginatedResponse;
use App\DTO\Common\PaginationResponse;


 readonly class ReservationService
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private TestSessionRepository $sessionRepository, // Injecté pour pouvoir persister la session
        private DocumentManager $documentManager,
        private ReservationMapper $reservationMapper
    ) {
    }

    public function createReservation(User $user, CreateReservationRequest $request): Reservation
    {

        $session = $this->sessionRepository->findActiveById($request->sessionId);

        if (!$session || (method_exists($session, 'isDeleted') && $session->isDeleted())) {
            throw new SessionNotFoundException();
        }


        if ($this->reservationRepository->exists($user, $session)) {
            throw new ReservationAlreadyExistsException();
        }

        $session->reserveSeat();

        $this->documentManager->persist($session);

        $reservation = new Reservation($user, $session);
        $this->reservationRepository->save($reservation); // Appelle persist() sur la réservation


        $this->documentManager->flush();

        return $reservation;
    }

    /**
     * @return Reservation[]
     */
    public function getUserReservations(User $user): array
    {
        return $this->reservationRepository->findByUser($user);
    }

    public function cancelReservation(User $user, string $id): void
    {
        $reservation = $this->reservationRepository->findById($id, $user);

        if (!$reservation || $reservation->isCancelled()) {
            throw new ReservationNotFoundException();
        }


        if (!$reservation->belongsTo($user)) {
            throw new UnauthorizedReservationAccessException();
        }


        $reservation->cancel();


        $session = $reservation->getSession();
        $session->releaseSeat();


        $this->documentManager->persist($session);
        $this->documentManager->persist($reservation);

        $this->documentManager->flush();
    }


    public function getUserReservationsPaginated(User $user, PaginationRequest $pagination): PaginatedResponse
    {
        $reservations = $this->documentManager
            ->createQueryBuilder(Reservation::class)
            ->field('user')->references($user)
            ->field('isCancelled')->equals(false)
            ->skip(($pagination->page - 1) * $pagination->limit)
            ->limit($pagination->limit)
            ->getQuery()
            ->execute()
            ->toArray();

        $total = $this->documentManager
            ->createQueryBuilder(Reservation::class)
            ->field('user')->references($user)
            ->field('isCancelled')->equals(false)
            ->count()
            ->getQuery()
            ->execute();

        $pages = (int) ceil($total / $pagination->limit);

        $reservationResponses = array_map(
            fn(Reservation $reservation) => $this->reservationMapper->mapToResponse($reservation),
            $reservations
        );

        return new PaginatedResponse(
            data: $reservationResponses,
            pagination: new PaginationResponse(
                page: $pagination->page,
                limit: $pagination->limit,
                total: $total,
                pages: $pages
            )
        );
    }
}
