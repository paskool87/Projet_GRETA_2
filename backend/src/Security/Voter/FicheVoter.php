<?php

namespace App\Security\Voter;

use App\Entity\Utilisateur;
use App\Entity\Fiche;
use App\Entity\SuiviPedagogique;
use App\Entity\Tutorat;
use App\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class FicheVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    public function __construct(
        private EntityManagerInterface $em
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Fiche;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /**
         * @var Utilisateur
         */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }
        $fiche = $subject;

        if ($user->getRole() == Role::ADMINISTRATEUR->label()) {
            return true;
        }


        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($fiche, $user);
                break;
            case self::VIEW:
                return $this->canView($fiche, $user);
                break;
        }

        return false;
    }
    private function canView(Fiche $fiche, Utilisateur $user): bool
    {

        switch (Role::tryfrom($user->getRole())) {
            case Role::PROFESSEUR_REFERENT:
                $fiche = $this->em->getRepository(SuiviPedagogique::class)->findFichebyProfesseurId($user->getId(), $fiche->getId());
                return $fiche != null;
                break;
            case Role::TUTEUR:
                $fiche = $this->em->getRepository(Tutorat::class)->findFichebyTuteurId($user->getId(), $fiche->getId());
                return $fiche != null;
                break;
            case Role::ALTERNANT:
                return $fiche->getAlternant()->getUtilisateur() === $user;
                break;
        }
        return false;
    }

    private function canEdit(Fiche $fiche, Utilisateur $user): bool
    {
        if ($this->canView($fiche, $user)) {
            if ($fiche->getValidateur() === 'entreprise' && $user->getRole() === Role::TUTEUR->name) {
                return true;
            } else if ($fiche->getValidateur() === 'formation' && $user->getRole() === Role::PROFESSEUR_REFERENT->name) {
                return true;
            } else if ($user->getRole() === Role::ALTERNANT->name) {
                return true;
            }
        }
        return false;
    }
}
