<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthenticationSuccessHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onSuccess'
        ];
    }

    public function onSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData(); // contient le token
        /**
         * @var Utilisateur
         */
        $user = $event->getUser();

        // 👈 Ajouter des données supplémentaires
        $data['message'] = 'Connexion réussie !';
        $data['user'] = [
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRole(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'id_utilisateur' => $user->getId(),
        ];

        $event->setData($data);
    }
}
