<?php

namespace App\Controller;

use App\Entity\Alternant;
use App\Entity\Fiche;;

use App\Repository\FicheRepository;
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
use App\Enum\StatusFiche;

#[Route('api/fiche')]
#[IsGranted('ROLE_ADMIN')]
final class FicheController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(name: 'app_fiche_index', methods: ['GET'])]
    public function index(
        FicheRepository $ficheRepository,
    ): JsonResponse {

        $fiches = $ficheRepository->findAll();

        return $this->json($fiches, Response::HTTP_OK, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('/{id}', name: 'app_fiche_show', methods: ['GET'])]
    public function show(?Fiche $fiche): JsonResponse
    {
        // Si l'fiche n'est pas trouvé
        if (null === $fiche) {
            return $this->json(["erreur" => "Fiche non trouve"], Response::HTTP_NOT_FOUND);
        }

        return $this->json(["fiche" => $fiche, "tache" => $fiche->getTaches(), "commentaire" => $fiche->getCommentaires()], 200, [], [
            'groups' => ['admin'],
        ]);
    }

    #[Route('', name: 'app_fiche_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            //On decode les données de la requete en se basant sur le modelle de l'entité "Fiche" et en creé une variable
            $fiche = $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Fiche::class,
                'json'
            );

            $data = json_decode($request->getContent(), true);
            $id = $data['Alternant_Id'];



            $alternant = $entityManager->getRepository(Alternant::class)->find($id);
            $fiche->setDateCreation(new DateTime("now"))->setStatusFiche(StatusFiche::BROUILLON)->setAlternantId($alternant);


            //On verifie si l'fiche est valde par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($fiche);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            // On enregistre l'fiche en base de données
            $entityManager->persist($fiche);
            $entityManager->flush();
            return $this->json($fiche->getAlternantId(), Response::HTTP_OK, [],  [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }



    #[Route('/{id}/edit', name: 'app_fiche_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Fiche $fiche, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'fiche n'est pas trouvé
            if (null === $fiche) {
                return $this->json(["erreur" => "Fiche non trouve"], Response::HTTP_NOT_FOUND);
            }

            //On decode les données de la requete en se basant sur le modelle de l'entité "Fiche" et on modifie l'fiche 
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Fiche::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $fiche]
            );

            //On verifie si l'fiche est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($fiche);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //On enregistre l'fiche modifié
            $entityManager->persist($fiche);
            $entityManager->flush();

            return $this->json($fiche, Response::HTTP_OK, [], [
                'groups' => ['admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'app_fiche_delete', methods: ['DELETE'])]
    public function delete(?Fiche $fiche, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'fiche n'est pas trouvé
            if (null === $fiche) {
                return $this->json(["erreur" => "Fiche non trouve"], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'fiche en base de données
            $entityManager->remove($fiche);
            $entityManager->flush();

            return $this->json(["message" => "Fiche suprime"], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }
}
