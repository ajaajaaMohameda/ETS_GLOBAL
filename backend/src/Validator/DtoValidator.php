<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class DtoValidator
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function validate(object $dto): void
    {
        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            throw new ValidationFailedException(
                $dto,
                $violations
            );
        }
    }
}