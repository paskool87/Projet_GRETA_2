<?php

namespace App\Controller;

use App\Entity\Alternant;
use App\Entity\Fiche;
use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Enum\StatusFiche;
use App\Repository\AlternantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/fiche')]
#[IsGranted('ROLE_USER')]
final class FicheController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route(name: 'app_fiche_index', methods: ['GET'])]
    public function index(
        AlternantRepository $alternantRepository,
        #[CurrentUser] Utilisateur $user,
    ): JsonResponse {
        $fiches = [];
        $role = Role::tryFrom($user->getRole());
        $datas = match ($role) {
            Role::ADMINISTRATEUR => $alternantRepository->findAll(),
            Role::PROFESSEUR_REFERENT => $user->getSuiviPedagogiques(),
            Role::TUTEUR => $user->getTutorats(),
            Role::ALTERNANT => [$alternantRepository->findOneBy(['utilisateur' => $user->getId()])],
            default => throw new BadRequestException('Role non reconnu'),
        };


        if ($role === Role::ALTERNANT || $role === Role::ADMINISTRATEUR) {
            foreach ($datas as $data) {
                $alternant = ['alternant' => $data, 'fiche' => $data->getFiches(), 'formation' => $data->getFormation()];
                $fiches[] = $alternant;
            }
        } else {
            foreach ($datas as $data) {
                $alternant = ['alternant' => $data->getAlternant(), 'fiche' => $data->getAlternant()->getFiches(), 'formation' => $data->getAlternant()->getFormation()];
                $fiches[] = $alternant;
            }
        }
        return $this->json($fiches, Response::HTTP_OK, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[IsGranted('VIEW', 'fiche')]
    #[Route('/{id}', name: 'app_fiche_show', methods: ['GET'])]
    public function show(?Fiche $fiche): JsonResponse
    {
        // Verification de l'existance de la fiche
        if (null === $fiche) {
            throw $this->createNotFoundException();
        }
        return $this->json(['fiche' => $fiche, 'alternant' => $fiche->getAlternant(), 'tache' => $fiche->getTaches(), 'commentaire' => $fiche->getCommentaires()], 200, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[IsGranted('ROLE_ALTERNANT')]
    #[Route('', name: 'app_fiche_new', methods: ['POST'])]
    public function new(Request $request, #[CurrentUser] Utilisateur $user): JsonResponse
    {
        // Decodage du corps de la requete
        $data = json_decode($request->getContent(), true);
        $id = $data['alternant_id'] ?? null;
        if (!$id) {
            throw new BadRequestHttpException('ID Alternant manquant');
        }

        // Verification de l'alternant
        $alternant = $this->entityManager->getRepository(Alternant::class)->find($id);
        if (!$alternant) {
            throw $this->createNotFoundException('Alternant non trouvé');
        }

        //Verification que l'alternant de la requete est bien celui de l'utilisateur connecté, ou que l'utilisateur est un admin
        if ($alternant->getUtilisateur() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas la permission de créer une fiche pour cet alternant');
        }

        // Bornes de la semaine actuelle (Lundi 00:00 au Vendredi 23:59)
        $debutSemaine = (new \DateTime('monday this week'))->setTime(0, 0, 0);
        $finSemaine = (new \DateTime('friday this week'))->setTime(23, 59, 59);

        // Chercher si une fiche existe déjà pour cette semaine précise
        $ficheExistante = $this->entityManager->getRepository(Fiche::class)->findOneBySemaine(
            $alternant,
            $debutSemaine,
            $finSemaine
        );

        // Envoi de la fiche deja crée
        if ($ficheExistante) {
            return $this->json(['fiche' => $ficheExistante, 'tache' => $ficheExistante->getTaches()], Response::HTTP_OK, [], ['groups' => 'user:read']);
        }

        // Création de la nouvelle fiche
        $fiche = new Fiche();
        $fiche->setStatusFiche(StatusFiche::BROUILLON)
            ->setAlternant($alternant)
            ->setDateDebut($debutSemaine)
            ->setDateFin($finSemaine);

        // Optionnel : creer les taches si elle sont presentes dans la requete

        // Validation de la fiche
        $errors = $this->validator->validate($fiche);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($fiche);
        $this->entityManager->flush();

        return $this->json($fiche, Response::HTTP_CREATED, [], ['groups' => 'user:read']);
    }

    #[IsGranted('EDIT', 'fiche')]
    #[Route('/{id}/edit', name: 'app_fiche_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Fiche $fiche, #[CurrentUser] Utilisateur $user): JsonResponse
    {
        // Si la fiche n'est pas trouvée
        if (null === $fiche) {
            throw $this->createNotFoundException('Fiche non trouvée');
        }

        // On decode les données de la requete
        $data = json_decode($request->getContent(), true);
        $statusString = $data['status'] ?? null;
        $validateur = $data['validateur'] ?? null;
        $validateur = strtolower($validateur);
        $status = StatusFiche::tryFrom(strtoupper($statusString));

        // Seul un alternant peut definir le validateur de sa fiche, et il ne peut choisir que "entreprise" ou "formation"
        if ($validateur && $this->isGranted('ROLE_ALTERNANT')) {
            if (in_array($validateur, ['entreprise', 'formation'])) {
                $fiche->setValidateur($validateur);
            } else {
                throw new BadRequestHttpException('Le validateur doit être entreprise ou formation');
            }
        }

        // on change l'etat de la fiche en fonction du role
        if ($statusString) {
            if (!$status) {
                throw new BadRequestHttpException('Statut inexistant');
            }
            switch ($status) {
                case StatusFiche::BROUILLON:
                case StatusFiche::SOUMISE:
                    if ($this->isGranted('ROLE_ALTERNANT')) {
                        $fiche->setStatusFiche($status)->setDateSoumission(new \DateTime());
                    } else {
                        throw $this->createAccessDeniedException("Action réservée à l'alternant");
                    }
                    break;
                case StatusFiche::VALIDE:
                case StatusFiche::CRITERES_NON_REMPLIS:
                    if ($this->isGranted('ROLE_TUTEUR') || $this->isGranted('ROLE_PROFESSEUR')) {
                        $fiche->setStatusFiche($status)->setDateValidation(new \DateTime());
                    } else {
                        throw $this->createAccessDeniedException('Action réservée au tuteur ou professeur référent');
                    }
                    break;
                default:
                    throw new BadRequestHttpException('Transition de statut non autorisée via cette route');
            }
        }

        // On verifie si la fiche est valide 
        $errors = $this->validator->validate($fiche);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        // On enregistre l'fiche modifié
        $this->entityManager->flush();

        return $this->json($fiche, Response::HTTP_OK, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_fiche_delete', methods: ['DELETE'])]
    public function delete(?Fiche $fiche): JsonResponse
    {
        // Si la fiche n'est pas trouvée
        if (null === $fiche) {
            throw $this->createNotFoundException('Fiche non trouvée');
        }

        // On supprime la fiche en base de données
        $this->entityManager->remove($fiche);
        $this->entityManager->flush();

        return $this->json(['message' => 'Fiche suprimée'], Response::HTTP_OK);
    }
}
