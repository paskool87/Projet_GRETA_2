<?php

// src/Security/CustomAccessDeniedHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class CustomAccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $exception): JsonResponse
    {
        // Si on a message on le retourne, sinon on retourne un message générique
        $message = $exception->getMessage() ? $exception->getMessage() : "Vous n'avez pas les droits pour accéder à cette ressource.";
        return new JsonResponse([
            'code' => Response::HTTP_FORBIDDEN,
            'message' => $message,
        ], Response::HTTP_FORBIDDEN);
    }
}
