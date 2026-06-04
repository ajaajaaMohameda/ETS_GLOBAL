<?php

namespace App\EventListener;

use App\Exception\EmailAlreadyUsedException;
use App\Exception\NoAvailableSeatsException;
use App\Exception\ReservationAlreadyExistsException;
use App\Exception\ReservationNotFoundException;
use App\Exception\SessionNotFoundException;
use App\Exception\UnauthorizedReservationAccessException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ApiExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            $event->setResponse($this->validationErrorResponse($exception));
            
            return;
        }

        if ($exception instanceof EmailAlreadyUsedException) {
            $event->setResponse(new JsonResponse([
                'message' => $exception->getMessage(),
            ], Response::HTTP_CONFLICT));

            return;
        }

        $statusCode = match (true) {
            $exception instanceof ReservationNotFoundException,
            $exception instanceof SessionNotFoundException => Response::HTTP_NOT_FOUND,
            $exception instanceof UnauthorizedReservationAccessException => Response::HTTP_FORBIDDEN,
            $exception instanceof ReservationAlreadyExistsException => Response::HTTP_CONFLICT,
            $exception instanceof NoAvailableSeatsException => Response::HTTP_BAD_REQUEST,
            default => null,
        };

        if ($statusCode !== null) {
            $event->setResponse(new JsonResponse([
                'message' => $exception->getMessage(),
            ], $statusCode));
        }
    }

    private function validationErrorResponse(ValidationFailedException $exception): JsonResponse
    {
        $errors = [];

        foreach ($exception->getViolations() as $violation) {
            $field = $violation->getPropertyPath();
            $errors[$field][] = $violation->getMessage();
        }

        return new JsonResponse([
            'message' => 'Validation failed.',
            'errors' => $errors,
        ], Response::HTTP_BAD_REQUEST);
    }
}