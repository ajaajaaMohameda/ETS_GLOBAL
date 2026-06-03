<?php

namespace App\Service\Session;

use App\Document\TestSession;
use App\DTO\Session\CreateSessionRequest;
use App\Repository\TestSessionRepository;

final readonly class SessionService
{
    public function __construct(
        private TestSessionRepository $repository,
    ) {
    }

    public function create(
        CreateSessionRequest $request
    ): TestSession {
        $session = new TestSession(
            language: $request->language,
            startsAt: new \DateTimeImmutable(
                $request->startsAt
            ),
            location: $request->location,
            capacity: $request->capacity,
        );

        $this->repository->save($session);

        return $session;
    }
}