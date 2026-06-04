<?php

namespace App\Controller\Profile;

use App\Mapper\UserMapper;
use App\Service\Profile\ProfileService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Validator\DtoValidator;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\Profile\UpdateProfileRequest;
final readonly class ProfileController
{
    public function __construct(
        private ProfileService $profileService,
        private UserMapper $userMapper,
        private SerializerInterface $serializer,
        private DtoValidator $dtoValidator,
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

    #[Route('/api/me', methods: ['PUT'])]
    public function update(
        Request $request
    ): JsonResponse
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            UpdateProfileRequest::class,
            'json'
        );

        $this->dtoValidator->validate($dto);

        $user = $this->profileService->updateProfile($dto);

        $response = $this->userMapper->toResponse($user);

        return new JsonResponse(
            $this->serializer->normalize($response),
            Response::HTTP_OK
        );
    }
    }