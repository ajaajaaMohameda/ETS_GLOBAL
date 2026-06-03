<?php

namespace App\Controller\Profile;

use App\Mapper\UserMapper;
use App\Service\Profile\ProfileService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ProfileController
{
    public function __construct(
        private ProfileService $profileService,
        private UserMapper $userMapper,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->profileService->getCurrentUser();

        $response = $this->userMapper->toResponse($user);

        return new JsonResponse(
            $this->serializer->normalize($response),
            Response::HTTP_OK
        );
    }
}