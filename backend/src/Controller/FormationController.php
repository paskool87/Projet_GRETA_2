<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/formation')]
#[IsGranted('ROLE_ADMIN')]
final class FormationController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route(name: 'app_formation_index', methods: ['GET'])]
    public function index(
        FormationRepository $formationRepository,
    ): JsonResponse {
        $formations = $formationRepository->findAll();

        return $this->json($formations, Response::HTTP_OK, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(?Formation $formation): JsonResponse
    {
        // Si l'formation n'est pas trouvé
        if (null === $formation) {
            return $this->json(['erreur' => 'Formation non trouve'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($formation, 200, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('', name: 'app_formation_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // On decode les données de la requete en se basant sur le modelle de l'entité "Formation" et en creé une variable
            $formation = $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Formation::class,
                'json'
            );

            $formation->setDateCreation(new \DateTime('now'));

            // On verifie si l'formation est valde par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($formation);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'formation en base de données
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->json($formation, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (\Exception $e) {
            return $this->json(['erreur' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Formation $formation, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'formation n'est pas trouvé
            if (null === $formation) {
                return $this->json(['erreur' => 'Formation non trouve'], Response::HTTP_NOT_FOUND);
            }

            // On decode les données de la requete en se basant sur le modelle de l'entité "Formation" et on modifie l'formation
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Formation::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $formation]
            );

            // On verifie si l'formation est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($formation);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'formation modifié
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->json($formation, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (\Exception $e) {
            return $this->json(['erreur' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['DELETE'])]
    public function delete(?Formation $formation, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'formation n'est pas trouvé
            if (null === $formation) {
                return $this->json(['erreur' => 'Formation non trouve'], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'formation en base de données
            $entityManager->remove($formation);
            $entityManager->flush();

            return $this->json(['message' => 'Formation suprime'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->json(['erreur' => $e->getMessage()], 500);
        }
    }
}
