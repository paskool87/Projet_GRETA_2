<?php

namespace App\Controller;

use App\Entity\Alternant;
use App\Entity\Formation;
use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Repository\AlternantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/alternant')]
#[IsGranted('ROLE_USER')]
final class AlternantController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {}

    #[Route(name: 'app_alternant_index', methods: ['GET'])]
    public function index(
        AlternantRepository $alternantRepository,
        #[CurrentUser] Utilisateur $user,
    ): JsonResponse {
        $alternants = match (Role::tryFrom($user->getRole())) {
            Role::ADMINISTRATEUR => $alternantRepository->findAll(),
            Role::TUTEUR, Role::PROFESSEUR_REFERENT => $alternantRepository->findAlternantByReferentID($user->getId()),
            Role::ALTERNANT => [$alternantRepository->findOneBy(['utilisateur' => $user])],
            default => throw new BadRequestException('Role non reconnu'),
        };

        if (!$alternants || count($alternants) === 0) {
            throw $this->createNotFoundException('Aucun alternant trouvé pour cet utilisateur');
        }

        $data = [];
        foreach ($alternants as $a) {
            $data[] = [
                'alternant_id' => $a->getId(),
                'utilisateur_alternant_id' => $a->getUtilisateur()->getId(),
                'identite' => $a->getUtilisateur()->getNom() . ' ' . $a->getUtilisateur()->getPrenom(),
                'derniere_fiche' => $a->getDerniereFiche(),
                'formation' => $a->getFormation(),
                'professeur_referent' => $a->getSuiviPedagogiques(),
                'tuteur' => $a->getTutorats(),
            ];
        }

        return $this->json($data, Response::HTTP_OK, [], [
            'groups' => ['user:read', 't:read', 'sp:read'],
        ]);
    }

    #[IsGranted('VIEW', subject: 'alternant')]
    #[Route('/{id}', name: 'app_alternant_show', methods: ['GET'])]
    public function show(?Alternant $alternant): JsonResponse
    {
        // Si l'alternant n'est pas trouvé
        if (null === $alternant) {
            throw $this->createNotFoundException('Alternant non trouve');
        }

        $data = [
            'alternant_id' => $alternant->getId(),
            'utilisateur_alternant_id' => $alternant->getUtilisateur()->getId(),
            'identite' => $alternant->getUtilisateur()->getNom() . ' ' . $alternant->getUtilisateur()->getPrenom(),
            'fiche' => $alternant->getFiches(),
            'formation' => $alternant->getFormation(),
            'professeur_referent' => $alternant->getSuiviPedagogiques(),
            'tuteur' => $alternant->getTutorats(),
        ];

        return $this->json($data, Response::HTTP_OK, [], [
            'groups' => ['user:read', 't:read', 'sp:read'],
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'app_alternant_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // On decode les données de la requete
        $data = json_decode($request->getContent(), true);
        $formationId = $data['formation_id'] ?? null;
        $utilisateurId = $data['utilisateur_id'] ?? null;

        if (null === $formationId || null === $utilisateurId) {
            throw new BadRequestException("Les champs 'formation_id' et 'utilisateur_id' sont requis");
        }

        // On verifie que la formation existe
        $formation = $entityManager->getRepository(Formation::class)->find($formationId);
        if (null === $formation) {
            throw $this->createNotFoundException('Formation non trouve');
        }

        // On verifie que l'utilisateur existe
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($utilisateurId);
        if (null === $utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouve');
        }

        // On verifie que l'utilisateur n'est pas déjà un alternant
        $existingAlternant = $entityManager->getRepository(Alternant::class)->findOneBy(['utilisateur' => $utilisateurId]);
        if (null !== $existingAlternant) {
            throw new BadRequestException('Utilisateur est deja un alternant');
        }

        // On crée un nouvel alternant et on lui assigne la formation et l'utilisateur
        $alternant = new Alternant();
        $alternant->setFormation($formation);
        $alternant->setUtilisateur($utilisateur);

        // On verifie si l'alternant est valide
        $errors = $this->validator->validate($alternant);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        // On enregistre l'alternant en base de données
        $entityManager->persist($alternant);
        $entityManager->flush();

        return $this->json($alternant, Response::HTTP_OK, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_alternant_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Alternant $alternant, EntityManagerInterface $entityManager): JsonResponse
    {
        // Si l'alternant n'est pas trouvé
        if (null === $alternant) {
            return $this->json(['erreur' => 'Alternant non trouve'], Response::HTTP_NOT_FOUND);
        }

        // On decode les données de la requete
        $data = json_decode($request->getContent(), true);
        $formationId = $data['formation_id'] ?? null;
        $utilisateurId = $data['utilisateur_id'] ?? null;
        $actif = $data['actif'] ?? null;

        // On modifie l'alternant en fonction des données reçues

        if (null !== $actif) {
            $alternant->setActif($actif);
        }

        if (!$formationId && $alternant->getFormation()->getId() != (int) $formationId) {
            // On verifie que la formation existe
            $formation = $entityManager->getRepository(Formation::class)->find($formationId);
            if (null === $formation) {
                throw $this->createNotFoundException('Formation non trouve');
            }
            $alternant->setFormation($formation);
        }

        if (!$utilisateurId && $alternant->getUtilisateur()->getId() != (int) $utilisateurId) {
            // On verifie que l'utilisateur existe
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($utilisateurId);
            if (null === $utilisateur) {
                throw $this->createNotFoundException('Utilisateur non trouve');
            }
            // On verifie que l'utilisateur n'est pas déjà un alternant
            $existingAlternant = $entityManager->getRepository(Alternant::class)->findOneBy(['utilisateur' => $utilisateurId]);
            if (null !== $existingAlternant) {
                throw new BadRequestException('Utilisateur est deja un alternant');
            }
            $alternant->setUtilisateur($utilisateur);
        }

        // On verifie si l'alternant est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
        $errors = $this->validator->validate($alternant);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        // On enregistre l'alternant modifié
        $entityManager->persist($alternant);
        $entityManager->flush();

        return $this->json($alternant, Response::HTTP_OK, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_alternant_delete', methods: ['DELETE'])]
    public function delete(?Alternant $alternant, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'alternant n'est pas trouvé
            if (null === $alternant) {
                return $this->json(['erreur' => 'Alternant non trouve'], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'alternant en base de données
            $entityManager->remove($alternant);
            $entityManager->flush();

            return $this->json(['message' => 'Alternant suprime'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->json(['erreur' => $e->getMessage()], 500);
        }
    }
}
