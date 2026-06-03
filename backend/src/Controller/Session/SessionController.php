<?php

namespace App\Controller\Session;

use App\DTO\Session\CreateSessionRequest;
use App\Mapper\SessionMapper;
use App\Service\Session\SessionService;
use App\Validator\DtoValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use App\DTO\Session\UpdateSessionRequest;


final readonly class SessionController
{
    public function __construct(
        private SessionService $sessionService,
        private SessionMapper $sessionMapper,
        private SerializerInterface $serializer,
        private DtoValidator $dtoValidator,
    ) {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/sessions', methods: ['POST'])]
    public function create(
        Request $request
    ): JsonResponse {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            CreateSessionRequest::class,
            'json'
        );

        $this->dtoValidator->validate($dto);

        $session = $this->sessionService->create($dto);

        $response = $this->sessionMapper
            ->toResponse($session);

        return new JsonResponse(
            $this->serializer->normalize($response),
            Response::HTTP_CREATED
        );
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/sessions/{id}', methods: ['PATCH'])]
    public function update(
        string $id,
        Request $request
    ): JsonResponse {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            UpdateSessionRequest::class,
            'json'
        );

        $this->dtoValidator->validate($dto);

        $session = $this->sessionService->update($id, $dto);

        $response = $this->sessionMapper->toResponse($session);

        return new JsonResponse(
            $this->serializer->normalize($response),
            Response::HTTP_OK
        );
    }
}