<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionListener
{
    #[AsEventListener]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // On ne traite que les routes qui commencent par /api (ou selon ton besoin)
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        $response = new JsonResponse([
            'erreur' => $exception->getMessage(),
            'code' => $statusCode
        ], $statusCode);

        $event->setResponse($response);
    }
}
