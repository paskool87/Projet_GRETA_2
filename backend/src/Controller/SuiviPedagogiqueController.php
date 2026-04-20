<?php

namespace App\Controller;

use App\Entity\SuiviPedagogique;

use App\Repository\SuiviPedagogiqueRepository;
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

#[Route('api/suiviPedagogique')]
#[IsGranted('ROLE_ADMIN')]
final class SuiviPedagogiqueController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(name: 'app_suiviPedagogique_index', methods: ['GET'])]
    public function index(
        SuiviPedagogiqueRepository $suiviPedagogiqueRepository,
    ): JsonResponse {

        $suiviPedagogiques = $suiviPedagogiqueRepository->findAll();

        return $this->json($suiviPedagogiques, Response::HTTP_OK, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('/{id}', name: 'app_suiviPedagogique_show', methods: ['GET'])]
    public function show(?SuiviPedagogique $suiviPedagogique): JsonResponse
    {
        // Si l'suiviPedagogique n'est pas trouvé
        if (null === $suiviPedagogique) {
            return $this->json(["erreur" => "SuiviPedagogique non trouve"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($suiviPedagogique, 200, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('', name: 'app_suiviPedagogique_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            //On decode les données de la requete en se basant sur le modelle de l'entité "SuiviPedagogique" et en creé une variable
            $suiviPedagogique = $this->serializer->deserialize(
                $request->getContent(), // body JSON
                SuiviPedagogique::class,
                'json'
            );

            $suiviPedagogique->setDateCreation(new DateTime("now"));

            //On verifie si l'suiviPedagogique est valde par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($suiviPedagogique);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'suiviPedagogique en base de données
            $entityManager->persist($suiviPedagogique);
            $entityManager->flush();

            return $this->json($suiviPedagogique, Response::HTTP_OK, [],  [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }



    #[Route('/{id}/edit', name: 'app_suiviPedagogique_edit', methods: ['PUT'])]
    public function edit(Request $request, ?SuiviPedagogique $suiviPedagogique, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'suiviPedagogique n'est pas trouvé
            if (null === $suiviPedagogique) {
                return $this->json(["erreur" => "SuiviPedagogique non trouve"], Response::HTTP_NOT_FOUND);
            }

            //On decode les données de la requete en se basant sur le modelle de l'entité "SuiviPedagogique" et on modifie l'suiviPedagogique 
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                SuiviPedagogique::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $suiviPedagogique]
            );

            //On verifie si l'suiviPedagogique est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($suiviPedagogique);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //On enregistre l'suiviPedagogique modifié
            $entityManager->persist($suiviPedagogique);
            $entityManager->flush();

            return $this->json($suiviPedagogique, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'app_suiviPedagogique_delete', methods: ['DELETE'])]
    public function delete(?SuiviPedagogique $suiviPedagogique, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'suiviPedagogique n'est pas trouvé
            if (null === $suiviPedagogique) {
                return $this->json(["erreur" => "SuiviPedagogique non trouve"], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'suiviPedagogique en base de données
            $entityManager->remove($suiviPedagogique);
            $entityManager->flush();

            return $this->json(["message" => "SuiviPedagogique suprime"], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }
}
