<?php

namespace App\Controller;

use App\Entity\Utilisateur;;

use App\Repository\UtilisateurRepository;
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

#[Route('api/utilisateur')]
#[IsGranted('ROLE_ADMIN')]
final class UtilisateurController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(
        UtilisateurRepository $utilisateurRepository,
    ): JsonResponse {

        $utilisateurs = $utilisateurRepository->findAll();

        return $this->json($utilisateurs, Response::HTTP_OK, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(?Utilisateur $utilisateur): JsonResponse
    {
        // Si l'utilisateur n'est pas trouvé
        if (null === $utilisateur) {
            return $this->json(["erreur" => "Utilisateur non trouve"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($utilisateur, 200, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('', name: 'app_utilisateur_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            //On decode les données de la requete en se basant sur le modelle de l'entité "Utilisateur" et en creé une variable
            $utilisateur = $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Utilisateur::class,
                'json'
            );

            $utilisateur->setDateCreation(new DateTime("now"));

            //On verifie si l'utilisateur est valde par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($utilisateur);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'utilisateur en base de données
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->json($utilisateur, Response::HTTP_OK, [],  [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }



    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Utilisateur $utilisateur, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'utilisateur n'est pas trouvé
            if (null === $utilisateur) {
                return $this->json(["erreur" => "Utilisateur non trouve"], Response::HTTP_NOT_FOUND);
            }

            //On decode les données de la requete en se basant sur le modelle de l'entité "Utilisateur" et on modifie l'utilisateur 
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Utilisateur::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $utilisateur]
            );

            //On verifie si l'utilisateur est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($utilisateur);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //On enregistre l'utilisateur modifié
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->json($utilisateur, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['DELETE'])]
    public function delete(?Utilisateur $utilisateur, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'utilisateur n'est pas trouvé
            if (null === $utilisateur) {
                return $this->json(["erreur" => "Utilisateur non trouve"], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'utilisateur en base de données
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            return $this->json(["message" => "Utilisateur suprime"], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }
}
