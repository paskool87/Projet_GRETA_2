<?php

namespace App\Controller;

use App\Entity\Alternant;
use App\Entity\Fiche;
use App\Entity\Utilisateur;
use App\Enum\Role;;

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
use App\Repository\AlternantRepository;
use App\Repository\SuiviPedagogiqueRepository;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('api/fiche')]
#[IsGranted('ROLE_USER')]
final class FicheController extends AbstractController
{

    public function __construct(
        private ValidatorInterface $validator
    ) {}

    #[Route(name: 'app_fiche_index', methods: ['GET'])]
    public function index(
        FicheRepository $ficheRepository,
        AlternantRepository $alternantRepository,
        #[CurrentUser] Utilisateur $user
    ): JsonResponse {
        switch (Role::tryFrom($user->getRole())) {
            case Role::ADMINISTRATEUR:
                $fiches = $ficheRepository->findAll();
                break;
            case Role::PROFESSEUR_REFERENT:
                $sps = $user->getSuiviPedagogiques();
                foreach ($sps as $sp) {
                    $alternant = ["utilisateur" => $sp->getAlternant()->getUtilisateur(), "fiche" => $sp->getAlternant()->getFiches()];
                    $fiches[] = $alternant;
                }
                break;
            case Role::TUTEUR:
                $sps = $user->getTutorats();
                foreach ($sps as $sp) {
                    $alternant = ["utilisateur" => $sp->getAlternant()->getUtilisateur(), "fiche" => $sp->getAlternant()->getFiches()];
                    $fiches[] = $alternant;
                }
                break;
            case Role::ALTERNANT:
                $alternant = $alternantRepository->findOneBy(['utilisateur' => $user->getId()]);
                $fiches = ["utilisateur" => $alternant->getUtilisateur(), "fiches" => $alternant->getFiches()];
                break;
            default:
                return $this->json(["erreur" => "erreur lors de la selection du role"], Response::HTTP_NOT_FOUND);
                break;
        }

        return $this->json($fiches, Response::HTTP_OK, [], [
            'groups' => ['user:read'],
        ]);
    }

    #[IsGranted('VIEW', 'fiche')]
    #[Route('/{id}', name: 'app_fiche_show', methods: ['GET'])]
    public function show(?Fiche $fiche): JsonResponse
    {
        // Verification de l'existance de la fiche
        if (null === $fiche) {
            throw $this->createNotFoundException();
        }

        return $this->json(["fiche" => $fiche, "alternant" => $fiche->getAlternant()->getUtilisateur(), "tache" => $fiche->getTaches(), "commentaire" => $fiche->getCommentaires()], 200, [], [
            'groups' => ['user:read'],
        ]);
    }


    #[IsGranted('ROLE_ALTERNANT')]
    #[Route('', name: 'app_fiche_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Decodage du corps de la requete
        $data = json_decode($request->getContent(), true);
        $id = $data['alternant_id'] ?? null;

        if (!$id) {
            throw new BadRequestHttpException('ID Alternant manquant');
        }


        // Verification de l'alternant
        $alternant = $entityManager->getRepository(Alternant::class)->find($id);
        if (!$alternant) {
            throw $this->createNotFoundException('Alternant non trouvé');
        }

        // Bornes de la semaine actuelle (Lundi 00:00 au Vendredi 23:59)
        $debutSemaine = (new DateTime('monday this week'))->setTime(0, 0, 0);
        $finSemaine = (new DateTime('friday this week'))->setTime(23, 59, 59);
        $aujourdhui = new DateTime("now");

        // Chercher si une fiche existe déjà pour cette semaine précise
        $ficheExistante = $entityManager->getRepository(Fiche::class)->findOneBySemaine(
            $alternant,
            $debutSemaine,
            $finSemaine
        );

        // Envoi de la fiche deja crée
        if ($ficheExistante) {
            return $this->json(['fiche' => $ficheExistante, 'tache' => $ficheExistante->getTaches()], Response::HTTP_OK, [], ['groups' => 'user:read']);
        }

        // Création de la nouvelle fiche 
        $fiche = new Fiche();
        $fiche->setStatusFiche(StatusFiche::BROUILLON)
            ->setAlternant($alternant)
            ->setDateDebut($debutSemaine)
            ->setDateFin($finSemaine);

        //Optionnel : creer les taches si elle sont presentes dans la requetes

        // Validation de la fiche
        $errors = $this->validator->validate($fiche);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }


        $entityManager->persist($fiche);
        $entityManager->flush();

        return $this->json($fiche, Response::HTTP_CREATED, [], ['groups' => 'user:read']);
    }


    #[isGranted('EDIT', 'fiche')]
    #[Route('/{id}/edit', name: 'app_fiche_edit', methods: ['PUT'])]
    public function edit(Request $request, ?Fiche $fiche, EntityManagerInterface $entityManager, #[CurrentUser] Utilisateur $user): JsonResponse
    {
        try {
            //1. Si l'fiche n'est pas trouvé
            if (null === $fiche) {
                return $this->json(["erreur" => "Fiche non trouve"], Response::HTTP_NOT_FOUND);
            }

            //2. On decode les données de la requete 
            $data = json_decode($request->getContent(), true);
            $statusString = $data["status"] ?? null;
            $validateur = $data["validateur"] ?? null;

            $role = $user->getRole();

            //3. On change le validateur 
            if ($validateur && $role === Role::ALTERNANT->name) {
                if (in_array($validateur, ["entreprise", "formation"])) {
                    $fiche->setValidateur($validateur);
                } else {
                    return $this->json(["erreur" => "validateur bad request"], Response::HTTP_BAD_REQUEST);
                }
            }


            //4. on change l'etat de la fiche en fonction du role
            if ($statusString) {
                $status = StatusFiche::tryFrom(strtoupper($statusString));
                if (!$status) {
                    return $this->json(["erreur" => "Statut inexistant"], Response::HTTP_BAD_REQUEST);
                }
                switch ($status) {
                    case StatusFiche::SOUMISE:
                        if ($role === Role::ALTERNANT->name) {
                            $fiche->setStatusFiche($status)->setDateSoumission(new DateTime());
                        } else {
                            return $this->json(["erreur" => "Action réservée à l'alternant"], Response::HTTP_FORBIDDEN);
                        };
                        break;
                    case StatusFiche::VALIDE:
                    case StatusFiche::CRITERES_NON_REMPLIS:
                        if ($role !== Role::ALTERNANT->name) {
                            $fiche->setStatusFiche($status)->setDateValidation(new DateTime());
                        } else {
                            return $this->json(["erreur" => "Un alternant ne peut pas valider sa fiche"], Response::HTTP_FORBIDDEN);
                        };
                        break;
                    default:
                        return $this->json(["erreur" => "Transition de statut non autorisée via cette route"], Response::HTTP_BAD_REQUEST);
                }
            }

            //5.On verifie si l'fiche est valide par rapport aux contraintes que l'on appliqué dans config/validator/validator.yaml
            $errors = $this->validator->validate($fiche);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            //6.On enregistre l'fiche modifié
            $entityManager->flush();

            return $this->json($fiche, Response::HTTP_OK, [], [
                'groups' => ['user:read'],
            ]);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }


    #[IsGranted('ROLE_ADMIN')]
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

            return $this->json(["message" => "Fiche suprime"], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(["erreur" => $e->getMessage()], 500);
        }
    }
}
