<?php

namespace App\Controller\Reservation;

use App\DTO\Reservation\CreateReservationRequest;
use App\Mapper\ReservationMapper;
use App\Service\Reservation\ReservationService;
use App\Validator\DtoValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\CurrentUserProvider;
use App\DTO\Common\PaginationRequest;


#[Route('/api/reservations')]
final readonly class ReservationController
{
    public function __construct(
        private ReservationService $reservationService,
        private CurrentUserProvider $currentUserProvider,
        private ReservationMapper $reservationMapper,
        private DtoValidator $dtoValidator,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('', name: 'api_reservations_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->currentUserProvider->getUser();
        
         $dto = $this->serializer->deserialize(
            $request->getContent(),
            CreateReservationRequest::class,
            'json'
        );

       $this->dtoValidator->validate($dto);

      

        $reservation = $this->reservationService->createReservation($user, $dto);
        $responseBody = $this->reservationMapper->mapToResponse($reservation);

        $json = $this->serializer->serialize($responseBody, 'json');

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }


    #[Route('', name: 'api_reservations_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $pagination = new PaginationRequest(
            page: max(1, $request->query->getInt('page', 1)),
            limit: min(100, $request->query->getInt('limit', 10))
        );

        $result = $this->reservationService->getUserReservationsPaginated(
            $this->currentUserProvider->getUser(),
            $pagination
        );

        return new JsonResponse(
            $this->serializer->normalize($result),
            Response::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'api_reservations_cancel', methods: ['DELETE'])]
    public function cancel(string $id): JsonResponse
    {
        $user = $this->currentUserProvider->getUser();
        $this->reservationService->cancelReservation($user, $id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}