<?php

namespace App\Controller;

use App\Dto\CreateReservationRequest;
use App\Mapper\ReservationMapper;
use App\Service\ReservationService;
use App\Service\CurrentUserProvider;
use App\Validator\DtoValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
        
        /** @var CreateReservationRequest $dto */
        $dto = $this->dtoValidator->deserializeAndValidate($request->getContent(), CreateReservationRequest::class);

        $reservation = $this->reservationService->createReservation($user, $dto);
        $responseBody = $this->reservationMapper->mapToResponse($reservation);

        $json = $this->serializer->serialize($responseBody, 'json');

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('', name: 'api_reservations_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->currentUserProvider->getUser();
        $reservations = $this->reservationService->getUserReservations($user);
        $responseBody = $this->reservationMapper->mapToResponseList($reservations);

        $json = $this->serializer->serialize($responseBody, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'api_reservations_cancel', methods: ['DELETE'])]
    public function cancel(string $id): JsonResponse
    {
        $user = $this->currentUserProvider->getUser();
        $this->reservationService->cancelReservation($user, $id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}