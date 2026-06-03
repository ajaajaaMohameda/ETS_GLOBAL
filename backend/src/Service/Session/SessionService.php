<?php

namespace App\Service\Session;

use App\Document\TestSession;
use App\DTO\Session\CreateSessionRequest;
use App\Repository\TestSessionRepository;
use App\DTO\Session\UpdateSessionRequest;
use App\Exception\SessionNotFoundException;

use App\DTO\Common\PaginationRequest;
use App\DTO\Common\PaginationResponse;
use App\DTO\Common\PaginatedResponse;
use App\Mapper\SessionMapper;

final readonly class SessionService
{
    public function __construct(
        private TestSessionRepository $repository,
        private SessionMapper $sessionMapper,
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

    public function delete(string $id): void
    {
        $session = $this->repository
            ->findActiveById($id);

        if (!$session) {
            throw new SessionNotFoundException();
        }

        $session->delete();

        $this->repository->save($session);
    }

    public function update(
        string $id,
        UpdateSessionRequest $request
    ): TestSession {
        $session = $this->repository->findActiveById($id);

        if (!$session) {
            throw new SessionNotFoundException();
        }

        $session->update(
            language: $request->language,
            startsAt: $request->startsAt !== null
                ? new \DateTimeImmutable($request->startsAt)
                : null,
            location: $request->location,
            capacity: $request->capacity,
        );

        $this->repository->save($session);

        return $session;
    }
    

    public function getPaginated(
        PaginationRequest $pagination
    ): PaginatedResponse {

        $sessions = $this->repository
            ->findPaginated($pagination);

        $total = $this->repository
            ->countActive();

        $pages = (int) ceil(
            $total / $pagination->limit
        );

    $sessionResponses = array_map(
        fn(TestSession $session)
            => $this->sessionMapper->toResponse($session),
        $sessions
    );
        return new PaginatedResponse(
            data: $sessionResponses,
            pagination: new PaginationResponse(
                page: $pagination->page,
                limit: $pagination->limit,
                total: $total,
                pages: $pages
            )
        );
    }

    public function getById(
        string $id
    ): TestSession {

        $session = $this->repository
            ->findActiveById($id);

        if (!$session) {
            throw new SessionNotFoundException();
        }

        return $session;
    }
    
}