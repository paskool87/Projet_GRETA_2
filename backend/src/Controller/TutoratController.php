<?php

namespace App\Controller;

use App\Entity\Tutorat;

use App\Repository\TutoratRepository;
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

#[Route('api/tutorat')]
#[IsGranted('ROLE_ADMIN')]
final class TutoratController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(name: 'app_tutorat_index', methods: ['GET'])]
    public function index(
        TutoratRepository $tutoratRepository,
    ): JsonResponse {

        $tutorats = $tutoratRepository->findAll();

        return $this->json($tutorats, Response::HTTP_OK, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('/{id}', name: 'app_tutorat_show', methods: ['GET'])]
    public function show(?Tutorat $tutorat): JsonResponse
    {
        // Si l'tutorat n'est pas trouvé
        if (null === $tutorat) {
            return $this->json(["erreur" => "Tutorat non trouve"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($tutorat, 200, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('', name: 'app_tutorat_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            //On decode les données de la requete en se basant sur le modelle de l'entité "Tutorat" et en creé une variable
            $tutorat = $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Tutorat::class,
                'json'
            );

            $tutorat->setDateCreation(new DateTime("now"));

            //On verifie si l'tutorat est valde par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($tutorat);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'tutorat en base de données
            $entityManager->persist($tutorat);
            $entityManager->flush();

            return $this->json($tutorat, Response::HTTP_OK, [],  [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }



    #[Route('/{id}/edit', name: 'app_tutorat_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Tutorat $tutorat, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'tutorat n'est pas trouvé
            if (null === $tutorat) {
                return $this->json(["erreur" => "Tutorat non trouve"], Response::HTTP_NOT_FOUND);
            }

            //On decode les données de la requete en se basant sur le modelle de l'entité "Tutorat" et on modifie l'tutorat 
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Tutorat::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $tutorat]
            );

            //On verifie si l'tutorat est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($tutorat);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //On enregistre l'tutorat modifié
            $entityManager->persist($tutorat);
            $entityManager->flush();

            return $this->json($tutorat, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'app_tutorat_delete', methods: ['DELETE'])]
    public function delete(?Tutorat $tutorat, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'tutorat n'est pas trouvé
            if (null === $tutorat) {
                return $this->json(["erreur" => "Tutorat non trouve"], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'tutorat en base de données
            $entityManager->remove($tutorat);
            $entityManager->flush();

            return $this->json(["message" => "Tutorat suprime"], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }
}
