<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Fiche;
use App\Entity\SuiviPedagogique;
use App\Entity\Tutorat;
use App\Entity\Utilisateur;
use App\Enum\Role;;

use App\Repository\CommentaireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/commentaire')]
#[IsGranted('ROLE_USER')]

final class CommentaireController extends AbstractController
{

    public function __construct(
        private ValidatorInterface $validator
    ) {}

    //A faire plus tard , pas d'importance pour l'instant
    #[IsGranted('ROLE_ADMIN')]
    #[Route(name: 'app_commentaire_index', methods: ['GET'])]
    public function index(
        CommentaireRepository $commentaireRepository,
    ): JsonResponse {

        $commentaires = $commentaireRepository->findAll();

        return $this->json($commentaires, Response::HTTP_OK, [], [
            'groups' => ['user:read'],
        ]);
    }

    //A faire plus tard , pas d'importance pour l'instant
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(?Commentaire $commentaire): JsonResponse
    {
        // Si l'commentaire n'est pas trouvé
        if (!$commentaire) {
            throw $this->createNotFoundException('Commentaire non trouvé');
        }

        return $this->json($commentaire, 200, [], [
            'groups' => ['user:read'],
        ]);
    }


    #[Route('', name: 'app_commentaire_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em, #[CurrentUser] Utilisateur $utilisateur): JsonResponse
    {
        //Seuls les professeur et tuteur peuvent mettre des commentaires
        if (!$this->isGranted('ROLE_PROFESSEUR') && !$this->isGranted('ROLE_TUTEUR')) {
            throw $this->createAccessDeniedException();
        }

        //On decode les données de la requete 
        $data = json_decode($request->getContent(), true);
        $fiche_id = $data['fiche_id'] ?? null;
        $commentaire_s = $data['commentaire'] ?? null;

        if (!$fiche_id || !$commentaire_s) {
            throw new BadRequestHttpException("Données manquantes (fiche_id ou commentaire)");
        }

        //On verifie la fiche et les droits de permission sur la fiche 
        $fiche = $em->getRepository(Fiche::class)->find($fiche_id);
        if (!$fiche) throw new BadRequestHttpException("La fiche spécifiée n'existe pas");

        if ($this->isGranted('EDIT', $fiche)) {
            throw $this->createAccessDeniedException();
        }

        // On crée un commentaire
        $commentaire = new Commentaire();
        $commentaire->setCommentaire($commentaire_s);
        $commentaire->setFiche($fiche);
        $commentaire->setAuteur($utilisateur);

        //On verifie le commentaire 
        $errors = $this->validator->validate($commentaire);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        //On enregistre le commentaire en base de données
        $em->persist($commentaire);
        $em->flush();

        return $this->json($commentaire, Response::HTTP_CREATED, [],  [
            'groups' => ['user:read'],
        ]);
    }



    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Commentaire $commentaire, EntityManagerInterface $entityManager, #[CurrentUser] Utilisateur $utilisateur): JsonResponse
    {
        // Verification du commentaire
        if (null === $commentaire) {
            throw $this->createNotFoundException();
        }

        // Seul l'auteur ou une personne ayant le rôle ADMIN peut modifier
        if ($commentaire->getAuteur() !== $utilisateur && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier ce commentaire.");
        }

        //On decode les données de la requete  
        $data = json_decode($request->getContent(), true);
        $commentaire_s = $data['commentaire'] ?? null;

        if (!$commentaire_s) {
            throw new BadRequestHttpException("Données manquantes (commentaire)");
        }

        $commentaire->setCommentaire($commentaire_s);

        //On verifie le commentaire 
        $errors = $this->validator->validate($commentaire);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        //On enregistre le commentaire modifié
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return $this->json($commentaire, Response::HTTP_OK, [], [
            'groups' => ['user:read'],
        ]);
    }


    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['DELETE'])]
    public function delete(#[CurrentUser] $utilisateur, ?Commentaire $commentaire, EntityManagerInterface $entityManager): JsonResponse
    {
        // Verification de l'existance du commentaire
        if (null === $commentaire) {
            return $this->json(["erreur" => "Commentaire non trouve"], Response::HTTP_NOT_FOUND);
        }

        // Seul l'auteur ou une personne ayant le rôle ADMIN peut modifier
        if ($commentaire->getAuteur() !== $utilisateur && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier ce commentaire.");
        }

        // On supprime le commentaire en base de données
        $entityManager->remove($commentaire);
        $entityManager->flush();

        return $this->json(["message" => "Commentaire supprimée"], Response::HTTP_ACCEPTED);
    }
}
