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
use App\DTO\Common\PaginationRequest;

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



    #[Route('/api/sessions', methods: ['GET'])]
    public function list(
        Request $request
    ): JsonResponse {

        $pagination = new PaginationRequest(
            page: max(
                1,
                $request->query->getInt('page', 1)
            ),
            limit: min(
                100,
                $request->query->getInt('limit', 10)
            )
        );

        $result = $this->sessionService
            ->getPaginated($pagination);

        return new JsonResponse(
            $this->serializer->normalize($result),
            Response::HTTP_OK
        );
    }

    #[Route('/api/sessions/{id}', methods: ['GET'])]
    public function get(
        string $id
    ): JsonResponse {

        $session = $this->sessionService
            ->getById($id);

        $response = $this->sessionMapper
            ->toResponse($session);

        return new JsonResponse(
            $this->serializer->normalize($response),
            Response::HTTP_OK
        );
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/sessions/{id}', methods: ['DELETE'])]
    public function delete(
        string $id
    ): JsonResponse {

        $this->sessionService
            ->delete($id);

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
    }