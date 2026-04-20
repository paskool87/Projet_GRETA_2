<?php

namespace App\Controller;

use App\Entity\Commentaire;;

use App\Repository\CommentaireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/commentaire')]
#[IsGranted('ROLE_ADMIN')]
final class CommentaireController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(name: 'app_commentaire_index', methods: ['GET'])]
    public function index(
        CommentaireRepository $commentaireRepository,
    ): JsonResponse {

        $commentaires = $commentaireRepository->findAll();

        return $this->json($commentaires, Response::HTTP_OK, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(?Commentaire $commentaire): JsonResponse
    {
        // Si l'commentaire n'est pas trouvé
        if (null === $commentaire) {
            return $this->json(["erreur" => "Commentaire non trouve"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($commentaire, 200, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('', name: 'app_commentaire_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            //On decode les données de la requete en se basant sur le modelle de l'entité "Commentaire" et en creé une variable
            $commentaire = $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Commentaire::class,
                'json'
            );

            $commentaire->setDateCreation(new DateTime("now"));

            //On verifie si l'commentaire est valde par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($commentaire);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'commentaire en base de données
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->json($commentaire, Response::HTTP_OK, [],  [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }



    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Commentaire $commentaire, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'commentaire n'est pas trouvé
            if (null === $commentaire) {
                return $this->json(["erreur" => "Commentaire non trouve"], Response::HTTP_NOT_FOUND);
            }

            //On decode les données de la requete en se basant sur le modelle de l'entité "Commentaire" et on modifie l'commentaire 
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Commentaire::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $commentaire]
            );

            //On verifie si l'commentaire est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($commentaire);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //On enregistre l'commentaire modifié
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->json($commentaire, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['DELETE'])]
    public function delete(?Commentaire $commentaire, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'commentaire n'est pas trouvé
            if (null === $commentaire) {
                return $this->json(["erreur" => "Commentaire non trouve"], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'commentaire en base de données
            $entityManager->remove($commentaire);
            $entityManager->flush();

            return $this->json(["message" => "Commentaire suprime"], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }
}
