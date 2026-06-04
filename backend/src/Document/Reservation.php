<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'reservations')]
class Reservation
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\ReferenceOne(targetDocument: User::class)]
    private User $user;

    #[ODM\ReferenceOne(targetDocument: TestSession::class)]
    private TestSession $session;

    #[ODM\Field(type: 'date_immutable')]
    private \DateTimeImmutable $reservedAt;

    #[ODM\Field(type: 'bool')]
    private bool $isCancelled = false;

    #[ODM\Field(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $cancelledAt = null;

    public function __construct(
        User $user,
        TestSession $session
    ) {
        $this->user = $user;
        $this->session = $session;
        $this->reservedAt = new \DateTimeImmutable();
    }

    public function cancel(): void
    {
        if ($this->isCancelled) {
            return;
        }

        $this->isCancelled = true;
        $this->cancelledAt = new \DateTimeImmutable();
    }

    public function belongsTo(User $user): bool
    {
        return $this->user->getId() === $user->getId();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getSession(): TestSession
    {
        return $this->session;
    }

    public function getReservedAt(): \DateTimeImmutable
    {
        return $this->reservedAt;
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }
}