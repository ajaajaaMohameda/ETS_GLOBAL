<?php

namespace App\Tests\Service;

use App\Document\Reservation;
use App\Document\TestSession;
use App\Document\User;
use App\DTO\Reservation\CreateReservationRequest;
use App\Exception\NoAvailableSeatsException;
use App\Exception\ReservationAlreadyExistsException;
use App\Repository\ReservationRepository;
use App\Repository\TestSessionRepository;
use App\Service\Reservation\ReservationService;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\TestCase;
use App\Mapper\ReservationMapper;

final class ReservationServiceTest extends TestCase
{
    private ReservationRepository $reservationRepositoryMock;
    private TestSessionRepository $sessionRepositoryMock;
    private DocumentManager $documentManagerMock;
    private ReservationMapper $reservationMapper;
    private ReservationService $reservationService;

    protected function setUp(): void
    {
        $this->reservationRepositoryMock = $this->createMock(ReservationRepository::class);
        $this->sessionRepositoryMock = $this->createMock(TestSessionRepository::class);
        $this->documentManagerMock = $this->createMock(DocumentManager::class);
         $this->reservationMapper = $this->createMock(ReservationMapper::class);


        $this->reservationService = new ReservationService(
            $this->reservationRepositoryMock,
            $this->sessionRepositoryMock,
            $this->documentManagerMock,
            $this->reservationMapper
        );
    }

    public function testCreateReservationThrowsExceptionWhenAlreadyExists(): void
    {
        $user = new User('Test', 'test@test.com', 'hash');
        // CORRECTION : 4 arguments exacts
        $session = new TestSession('English', new \DateTimeImmutable(), 'Paris', 10);
        
        $request = new CreateReservationRequest();
        $request->sessionId = 'session123';

        $this->sessionRepositoryMock->method('findActiveById')->willReturn($session);
        $this->reservationRepositoryMock->method('exists')->willReturn(true);

        $this->expectException(ReservationAlreadyExistsException::class);

        $this->reservationService->createReservation($user, $request);
    }

    public function testCreateReservationThrowsExceptionWhenNoSeats(): void
    {
        $user = new User('Test', 'test@test.com', 'hash');
        // CORRECTION : 4 arguments exacts, capacité à 0
        $session = new TestSession('English', new \DateTimeImmutable(), 'Paris', 0);
        
        $request = new CreateReservationRequest();
        $request->sessionId = 'session123';

        $this->sessionRepositoryMock->method('findActiveById')->willReturn($session);
        $this->reservationRepositoryMock->method('exists')->willReturn(false);

        $this->expectException(NoAvailableSeatsException::class);

        $this->reservationService->createReservation($user, $request);
    }

    public function testCreateReservationSuccess(): void
    {
        $user = new User('Test', 'test@test.com', 'hash');
        // CORRECTION : 4 arguments exacts
        $session = new TestSession('English', new \DateTimeImmutable(), 'Paris', 10);
        
        $request = new CreateReservationRequest();
        $request->sessionId = 'session123';

        $this->sessionRepositoryMock->method('findActiveById')->willReturn($session);
        $this->reservationRepositoryMock->method('exists')->willReturn(false);

        $this->documentManagerMock->expects($this->once())->method('persist');
        $this->reservationRepositoryMock->expects($this->once())->method('save');
        $this->documentManagerMock->expects($this->once())->method('flush');

        $reservation = $this->reservationService->createReservation($user, $request);

        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertEquals(9, $session->getAvailableSeats());
    }
}