<?php

namespace App\Controller\Auth;

use App\DTO\Auth\RegisterUserRequest;
use App\Service\Auth\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Mapper\UserMapper;

final readonly class AuthController
{
    public function __construct(
        private AuthService $authService,
        private SerializerInterface $serializer,
        private UserMapper $userMapper,
    ) {
    }

    #[Route('/api/register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            RegisterUserRequest::class,
            'json'
        );

        $user = $this->authService->register($dto);

        $response = $this->userMapper->toResponse($user);

        return new JsonResponse(
            $this->serializer->normalize($response),
            Response::HTTP_CREATED
        );
    }
}