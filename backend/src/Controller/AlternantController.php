<?php

namespace App\Controller;

use App\Entity\Alternant;;

use App\Repository\AlternantRepository;
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

#[Route('api/alternant')]
#[IsGranted('ROLE_ADMIN')]
final class AlternantController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(name: 'app_alternant_index', methods: ['GET'])]
    public function index(
        AlternantRepository $alternantRepository,
    ): JsonResponse {

        $alternants = $alternantRepository->findAll();

        return $this->json($alternants, Response::HTTP_OK, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('/{id}', name: 'app_alternant_show', methods: ['GET'])]
    public function show(?Alternant $alternant): JsonResponse
    {
        // Si l'alternant n'est pas trouvé
        if (null === $alternant) {
            return $this->json(["erreur" => "Alternant non trouve"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($alternant, 200, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('', name: 'app_alternant_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            //On decode les données de la requete en se basant sur le modelle de l'entité "Alternant" et en creé une variable
            $alternant = $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Alternant::class,
                'json'
            );

            $alternant->setDateCreation(new DateTime("now"));

            //On verifie si l'alternant est valde par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($alternant);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'alternant en base de données
            $entityManager->persist($alternant);
            $entityManager->flush();

            return $this->json($alternant, Response::HTTP_OK, [],  [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }



    #[Route('/{id}/edit', name: 'app_alternant_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Alternant $alternant, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'alternant n'est pas trouvé
            if (null === $alternant) {
                return $this->json(["erreur" => "Alternant non trouve"], Response::HTTP_NOT_FOUND);
            }

            //On decode les données de la requete en se basant sur le modelle de l'entité "Alternant" et on modifie l'alternant 
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Alternant::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $alternant]
            );

            //On verifie si l'alternant est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($alternant);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //On enregistre l'alternant modifié
            $entityManager->persist($alternant);
            $entityManager->flush();

            return $this->json($alternant, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'app_alternant_delete', methods: ['DELETE'])]
    public function delete(?Alternant $alternant, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'alternant n'est pas trouvé
            if (null === $alternant) {
                return $this->json(["erreur" => "Alternant non trouve"], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'alternant en base de données
            $entityManager->remove($alternant);
            $entityManager->flush();

            return $this->json(["message" => "Alternant suprime"], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }
}
