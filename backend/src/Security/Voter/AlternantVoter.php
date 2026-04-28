<?php

namespace App\Security\Voter;

use App\Entity\Alternant;
use App\Entity\Utilisateur;
use App\Enum\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

final class AlternantVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // On vérifie les deux attributs maintenant
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Alternant;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Utilisateur) {
            return false;
        }

        // Un admin peut TOUT faire (VIEW et EDIT)
        if ($user->getRole() === Role::ADMINISTRATEUR->name) {
            return true;
        }

        /** @var Alternant $alternant */
        $alternant = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($alternant, $user),
            self::EDIT => $this->canEdit($alternant, $user),
            default => false,
        };
    }

    private function canView(Alternant $alternant, Utilisateur $user): bool
    {
        // Utilisation du match pour la clarté
        return match (Role::tryFrom($user->getRole())) {
            Role::PROFESSEUR_REFERENT => $this->isProfesseurDeLAlternant($alternant, $user),
            Role::TUTEUR => $this->isTuteurDeLAlternant($alternant, $user),
            Role::ALTERNANT => $alternant->getUtilisateur() === $user,
            default => false,
        };
    }

    private function canEdit(Alternant $alternant, Utilisateur $user): bool
    {
        // Seul l'alternant lui-même peut modifier ses infos de base
        return $alternant->getUtilisateur() === $user;
    }

    // Extraction des boucles dans des méthodes privées pour la lisibilité
    private function isProfesseurDeLAlternant(Alternant $alternant, Utilisateur $user): bool
    {
        foreach ($alternant->getSuiviPedagogiques() as $suivi) {
            if ($suivi->getProfesseur() === $user) {
                return true;
            }
        }
        return false;
    }

    private function isTuteurDeLAlternant(Alternant $alternant, Utilisateur $user): bool
    {
        foreach ($alternant->getTutorats() as $tutorat) {
            if ($tutorat->getTuteur() === $user) {
                return true;
            }
        }
        return false;
    }
}
