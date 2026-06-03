<?php

namespace App\EventListener;

use App\Exception\EmailAlreadyUsedException;
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