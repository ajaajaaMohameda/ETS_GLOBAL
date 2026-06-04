<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use App\Exception\NoAvailableSeatsException;

#[ODM\Document(collection: 'sessions')]
class TestSession
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $language;

    #[ODM\Field(type: 'date_immutable')]
    private \DateTimeImmutable $startsAt;

    #[ODM\Field(type: 'string')]
    private string $location;

    #[ODM\Field(type: 'int')]
    private int $capacity;

    #[ODM\Field(type: 'int')]
    private int $availableSeats;

    #[ODM\Field(type: 'bool')]
    private bool $isDeleted = false;

    #[ODM\Field(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct(
        string $language,
        \DateTimeImmutable $startsAt,
        string $location,
        int $capacity
    ) {
        $this->language = trim($language);
        $this->startsAt = $startsAt;
        $this->location = trim($location);
        $this->capacity = $capacity;
        $this->availableSeats = $capacity;
    }

    public function decreaseAvailableSeats(): void
    {
        if ($this->availableSeats <= 0) {
            throw new \DomainException(
                'No seats available.'
            );
        }

        $this->availableSeats--;
    }

    public function increaseAvailableSeats(): void
    {
        if ($this->availableSeats < $this->capacity) {
            $this->availableSeats++;
        }
    }

    public function delete(): void
    {
        if ($this->isDeleted) {
            return;
        }

        $this->isDeleted = true;
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function update(
        ?string $language,
        ?\DateTimeImmutable $startsAt,
        ?string $location,
        ?int $capacity
    ): void {
        if ($language !== null) {
            $this->language = trim($language);
        }

        if ($startsAt !== null) {
            $this->startsAt = $startsAt;
        }

        if ($location !== null) {
            $this->location = trim($location);
        }

        if ($capacity !== null) {
            $reservedSeats = $this->capacity - $this->availableSeats;

            if ($capacity < $reservedSeats) {
                throw new InvalidSessionCapacityException();
            }

            $this->availableSeats += ($capacity - $this->capacity);
            $this->capacity = $capacity;
        }
    }

    public function reserveSeat(): void
    {
        if ($this->availableSeats <= 0) {
            throw new NoAvailableSeatsException();
        }

        $this->availableSeats--;
    }

    public function releaseSeat(): void
    {
        if ($this->availableSeats < $this->capacity) {
            $this->availableSeats++;
        }
    }

    // ==========================================
    // GETTERS
    // ==========================================

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    // AJOUT : Getter pour la date de début de session
    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->startsAt;
    }

    // AJOUT : Getter pour le lieu
    public function getLocation(): string
    {
        return $this->location;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getAvailableSeats(): int
    {
        return $this->availableSeats;
    }
}