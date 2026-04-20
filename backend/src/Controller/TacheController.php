<?php

namespace App\Controller;

use App\Entity\Tache;

use App\Repository\TacheRepository;
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

#[Route('api/tache')]
#[IsGranted('ROLE_ADMIN')]
final class TacheController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(name: 'app_tache_index', methods: ['GET'])]
    public function index(
        TacheRepository $tacheRepository,
    ): JsonResponse {

        $taches = $tacheRepository->findAll();

        return $this->json($taches, Response::HTTP_OK, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('/{id}', name: 'app_tache_show', methods: ['GET'])]
    public function show(?Tache $tache): JsonResponse
    {
        // Si l'tache n'est pas trouvé
        if (null === $tache) {
            return $this->json(["erreur" => "Tache non trouve"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($tache, 200, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('', name: 'app_tache_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            //On decode les données de la requete en se basant sur le modelle de l'entité "Tache" et en creé une variable
            $tache = $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Tache::class,
                'json'
            );

            $tache->setDateCreation(new DateTime("now"));

            //On verifie si l'tache est valde par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($tache);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'tache en base de données
            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->json($tache, Response::HTTP_OK, [],  [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }



    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Tache $tache, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'tache n'est pas trouvé
            if (null === $tache) {
                return $this->json(["erreur" => "Tache non trouve"], Response::HTTP_NOT_FOUND);
            }

            //On decode les données de la requete en se basant sur le modelle de l'entité "Tache" et on modifie l'tache 
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Tache::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $tache]
            );

            //On verifie si l'tache est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($tache);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //On enregistre l'tache modifié
            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->json($tache, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'app_tache_delete', methods: ['DELETE'])]
    public function delete(?Tache $tache, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'tache n'est pas trouvé
            if (null === $tache) {
                return $this->json(["erreur" => "Tache non trouve"], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'tache en base de données
            $entityManager->remove($tache);
            $entityManager->flush();

            return $this->json(["message" => "Tache suprime"], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }
}
