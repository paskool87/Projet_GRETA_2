<?php

namespace App\Controller;

use App\Entity\Alternant;
use App\Entity\Formation;
use App\Entity\SuiviPedagogique;
use App\Entity\Tutorat;
use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_USER')]
#[Route('api/utilisateur')]
final class UtilisateurController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Route(name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(
        #[CurrentUser] Utilisateur $utilisateurConnecte
    ): JsonResponse {
        return $this->json($utilisateurConnecte, Response::HTTP_OK, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(?Utilisateur $utilisateur, #[CurrentUser] Utilisateur $utilisateurconnecte, EntityManagerInterface $entityManager): JsonResponse
    {
        // Si l'utilisateur n'est pas trouvé
        if (null === $utilisateur || !$utilisateur->isActif()) {
            throw $this->createNotFoundException('Utilisateur non trouve ou desactive');
        }

        if ($utilisateur->getId() === $utilisateurconnecte->getId()) {
            return $this->json($utilisateur, 200, [], [
                'groups' => ['user:read'],
            ]);
        }

        if ($utilisateur->getId() === $utilisateurconnecte->getId()) {
            return $this->json($utilisateur, 200, [], [
                'groups' => ['user:read'],
            ]);
        }


        $role = $utilisateurconnecte->getRole();
        if ($role === Role::TUTEUR->name) {
            // On verifie que le tuteur a le droit de voir les informations de cet utilisateur (il doit être le tuteur d'un alternant qui est lié à cet utilisateur)
            $alternant = $entityManager->getRepository(Tutorat::class)->findAlternantByTuteurId($utilisateurconnecte->getId(), $utilisateur->getId());
            if (null === $alternant) {
                throw $this->createAccessDeniedException('Permission non accorde');
            }

            return $this->json($utilisateur, 200, [], [
                'groups' => ['user:read'],
            ]);
        } elseif ($role === Role::PROFESSEUR_REFERENT->name) {
            // On verifie que le tuteur a le droit de voir les informations de cet utilisateur (il doit être le tuteur d'un alternant qui est lié à cet utilisateur)
            $alternant = $entityManager->getRepository(SuiviPedagogique::class)->findAlternantByProfesseurId($utilisateurconnecte->getId(), $utilisateur->getId());
            if (null === $alternant) {
                throw $this->createAccessDeniedException('Permission non accorde');
            }

            return $this->json($utilisateur, 200, [], [
                'groups' => ['user:read'],
            ]);
        }

        return $this->json($utilisateur, 200, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[Route('', name: 'app_utilisateur_new', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // On decode les données de la requete en se basant sur le modelle de l'entité "Utilisateur" et en creé une variable
        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'] ?? null;
        $prenom = $data['prenom'] ?? null;
        $email = $data['email'] ?? null;
        $role = $data['role'] ?? null;
        $motdepasse = $data['motdepasse'] ?? null;

        if (!$nom || !$prenom || !$email || !$role || !$motdepasse) {
            throw new BadRequestException('Données incompletes (nom, prenom, email, role ou motdepasse manquant)');
        }

        $role = strtoupper($role);
        // On verifie que le role est valide
        $role_a_tester = array_map(fn($r) => $r->name, Role::cases());
        if (!in_array($role, $role_a_tester)) {
            throw new BadRequestException('Role invalide (doit etre un des suivants : ' . implode(', ', $role_a_tester) . ')');
        }

        // On verifie que l'email n'est pas deja utilisé
        $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            throw new BadRequestException('Email deja utilise');
        }

        // On crée un utilisateur et on lui affecte les données de la requete
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setEmail($email);
        $utilisateur->setRole($role);
        $utilisateur->setMotDePasse($this->passwordHasher->hashPassword($utilisateur, $motdepasse));
        $utilisateur->setDateCreation(new \DateTime('now'));


        $formation_id = $data['formation_id'] ?? null;
        if ($role === Role::ALTERNANT->name) {
            if (!$formation_id) {
                throw new BadRequestException('ID de formation requis pour un alternant');
            }
            $formation = $entityManager->getRepository(Formation::class)->find($formation_id);
            if (null === $formation) {
                throw new BadRequestException('Formation non trouve');
            }
            $alternant = new Alternant();
            $alternant->setUtilisateur($utilisateur);
            $alternant->setFormation($formation);
            $entityManager->persist($alternant);

            // On verifie si l'utilisateur est valide
            $errors = $this->validator->validate($alternant);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }
        }

        // On verifie si l'utilisateur est valide 
        $errors = $this->validator->validate($utilisateur);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        // On enregistre l'utilisateur en base de données
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        return $this->json($utilisateur, Response::HTTP_CREATED, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Utilisateur $utilisateur, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'utilisateur n'est pas trouvé
            if (null === $utilisateur) {
                return $this->json(['erreur' => 'Utilisateur non trouve'], Response::HTTP_NOT_FOUND);
            }
            // on verifie que l'utilisateur connecté a le droit de modifier cet utilisateur
            $utilisateurConnecte = $this->getUser();
            if ($utilisateurConnecte != $utilisateur && !$this->isGranted('ROLE_ADMIN')) {
                throw $this->createAccessDeniedException('Permission non accorde');
            }

            // On decode les données de la requete
            $data = json_decode($request->getContent(), true);
            $nom = $data['nom'] ?? null;
            $prenom = $data['prenom'] ?? null;
            $email = $data['email'] ?? null;
            $motdepasse = $data['motdepasse'] ?? null;

            if ($email !== $utilisateur->getEmail() && $email) {
                // On verifie que l'email n'est pas deja utilisé
                $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    throw new BadRequestException('Email deja utilise');
                }
                $utilisateur->setEmail($email);
            }
            if ($nom !== $utilisateur->getNom() && $nom) {
                $utilisateur->setNom($nom);
            }
            if ($prenom !== $utilisateur->getPrenom() &&  $prenom) {
                $utilisateur->setPrenom($prenom);
            }
            if ($motdepasse) {
                $utilisateur->setMotDePasse($this->passwordHasher->hashPassword($utilisateur, $motdepasse));
            }
            // On verifie si l'utilisateur est valide
            $errors = $this->validator->validate($utilisateur);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'utilisateur modifié
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->json($utilisateur, Response::HTTP_OK, [], [
                'groups' => ['user:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json(['erreur' => $e->getMessage()], 500);
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['DELETE'])]
    public function delete(?Utilisateur $utilisateur, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'utilisateur n'est pas trouvé
            if (null === $utilisateur) {
                return $this->json(['erreur' => 'Utilisateur non trouve'], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'utilisateur en base de données
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            return $this->json(['message' => 'Utilisateur suprime'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->json(['erreur' => $e->getMessage()], 500);
        }
    }
}
