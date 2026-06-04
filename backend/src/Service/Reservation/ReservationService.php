<?php

namespace App\Service;

use App\Document\Reservation;
use App\Document\User;
use App\Dto\CreateReservationRequest;
use App\Exception\NoAvailableSeatsException;
use App\Exception\ReservationAlreadyExistsException;
use App\Exception\ReservationNotFoundException;
use App\Exception\SessionNotFoundException;
use App\Exception\UnauthorizedReservationAccessException;
use App\Repository\ReservationRepository;
use App\Repository\TestSessionRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

final readonly class ReservationService
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private TestSessionRepository $sessionRepository, // Injecté pour pouvoir persister la session
        private DocumentManager $documentManager
    ) {
    }

    public function createReservation(User $user, CreateReservationRequest $request): Reservation
    {

        $session = $this->sessionRepository->findById($request->sessionId);
        
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
        $reservation = $this->reservationRepository->findById($id);

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
}