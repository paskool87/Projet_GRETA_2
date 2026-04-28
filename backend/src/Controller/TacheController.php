<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Entity\Tache;
use App\Repository\FicheRepository;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/tache')]
#[IsGranted('ROLE_USER')]
final class TacheController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {}

    #[IsGranted('ROLE_ADMIN')]
    #[Route(name: 'app_tache_index', methods: ['GET'])]
    public function index(
        TacheRepository $tacheRepository,
    ): JsonResponse {
        $taches = $tacheRepository->findAll();

        return $this->json($taches, Response::HTTP_OK, [], [
            'groups' => ['user', 'admin'],
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_tache_show', methods: ['GET'])]
    public function show(?Tache $tache): JsonResponse
    {
        // Si l'tache n'est pas trouvé
        if (null === $tache) {
            return $this->json(['erreur' => 'Tache non trouve'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($tache, 200, [], [
            'groups' => ['user', 'admin'],
        ]);
    }

    #[IsGranted('ROLE_ALTERNANT')]
    #[Route('', name: 'app_tache_new', methods: ['POST', 'PUT'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FicheRepository $ficheRepository): JsonResponse
    {
        try {
            // On decode les données de la requete
            $data = json_decode($request->getContent(), true);
            $fiche_id = $data['fiche_id'] ?? null;
            $taches = $data['taches'] ?? null;

            if (!$fiche_id || !$taches) {
                throw new BadRequestException('Mauvais parametrages(fiche_id ou taches manquantes)');
            }

            //  On recupere la fiche et on verifie si on peut l'a modifier
            $fiche = $ficheRepository->find($fiche_id);
            if (!$fiche) {
                throw new BadRequestException('Fiche non trouve');
            }
            if (!$this->isGranted('EDIT', $fiche)) {
                throw $this->createAccessDeniedException();
            }

            // Creer ou modifier les taches
            foreach ($taches as $t) {
                $description = $t['description'] ?? null;
                $categorie = $t['categorie'] ?? null;
                $date_tache = $t['date_tache'] ?? null;

                if (!$description || !$categorie || !$date_tache) {
                    throw new BadRequestException('Mauvais parametrages');
                }

                // On verifie si la date de la tache est dans les espaces de la fiches
                $debutfiche = $fiche->getDateDebut()->format('Y-m-d');
                $finfiche = $fiche->getDateFin()->format('Y-m-d');

                // La tache est hors de la range de la fiche
                if ($date_tache < $debutfiche || $date_tache > $finfiche) {
                    throw new BadRequestException('Date hors de la plage de la fiche');
                }

                // On creer la tache si elle n'existe pas ou on la modifie
                $tache = $entityManager->getRepository(Tache::class)->findOneTachebyDate(new \DateTime($date_tache), $fiche_id);
                if (null === $tache) {
                    $tache = new Tache();
                    $tache->setDateTache(new \DateTime($date_tache));
                    $tache->setFiche($fiche);
                }
                $tache->setCategorie($categorie);
                $tache->setDescription($description);

                // On verifie si la tache est valide par rapport aux contraintes
                $errors = $this->validator->validate($tache);
                if (count($errors) > 0) {
                    return $this->json($errors, Response::HTTP_BAD_REQUEST);
                }
                $fiche->addTach($tache);
                $entityManager->persist($tache);
            }
            $entityManager->flush();
            // On enregistre la ou les taches en base de données

            return $this->json($fiche->getTaches(), Response::HTTP_OK, [], [
                'groups' => ['user:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json(['erreur' => $e->getMessage()], 500);
        }
    }

    /* Avoir avec mon mentor ou nicolas au niveau de la pertinance
    #[IsGranted('ROLE_ALTERNANT')]
    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Tache $tache, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'tache n'est pas trouvé
            if (null === $tache) {
                return $this->json(["erreur" => "Tache non trouve"], Response::HTTP_NOT_FOUND);
            }
            $data = json_decode($request->getContent(), true);
            //On decode les données de la requete en se basant sur le modelle de l'entité "Tache" et on modifie l'tache
            $this->serializer->deserialize(
                $request->getContent(), // body JSON
                Tache::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $tache,
                    [AbstractNormalizer::ATTRIBUTES => ['tache_acomplie', 'date_tache']]
                ]
            );
            if (isset($data['categorie'])) {
                $tache->setCategorie($data['categorie']);
            }

            //On verifie si l'tache est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($tache);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //On enregistre l'tache modifié
            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->json($tache, Response::HTTP_OK, [], [
                'groups' => ['user', 'admin'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }*/

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_tache_delete', methods: ['DELETE'])]
    public function delete(?Tache $tache, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Si l'tache n'est pas trouvé
            if (null === $tache) {
                return $this->json(['erreur' => 'Tache non trouve'], Response::HTTP_NOT_FOUND);
            }

            // On supprime l'tache en base de données
            $entityManager->remove($tache);
            $entityManager->flush();

            return $this->json(['message' => 'Tache suprime'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return $this->json(['erreur' => $e->getMessage()], 500);
        }
    }
}
