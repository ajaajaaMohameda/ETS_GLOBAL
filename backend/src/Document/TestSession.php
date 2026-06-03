<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

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
        $this->isDeleted = true;
        $this->deletedAt = new \DateTimeImmutable();
    }
}