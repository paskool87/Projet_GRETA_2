<?php
// src/Security/CustomAccessDeniedHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CustomAccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $exception): JsonResponse
    {
        return new JsonResponse([
            'code'    => Response::HTTP_FORBIDDEN,
            'message' => "Vous n'avez pas les droits pour accéder à cette ressource.",
        ], Response::HTTP_FORBIDDEN);
    }
}
