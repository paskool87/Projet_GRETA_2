<?php

namespace App\DataFixtures;

use App\Entity\Alternant;
use App\Entity\Commentaire;
use App\Entity\Fiche;
use App\Entity\Formation;
use App\Entity\SuiviPedagogique;
use App\Entity\Tache;
use App\Entity\Tutorat;
use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Enum\StatusFiche;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        date_default_timezone_set('UTC');

        $alternants = [];
        $professeurs = [];

        // Récupération des données calculées
        $formation_presets = $this->genererFormationsAutomatique();

        foreach ($formation_presets as $data) {
            // 1. Création de l'entité Formation
            $formation = new Formation();
            $formation->setNomFormation($data['nom']) // Utilise le nom du preset
                ->setDescription($data['descrription'])
                ->setSession($data['session'])
                ->setDateDebut(new \DateTime($data['datedebut'])) // Utilise la date calculée
                ->setDateFin(new \DateTime($data['datefin']));

            $manager->persist($formation);

            // 2. Création du professeur référent pour cette formation
            $professeur = $this->creerUtilisateur(
                Role::PROFESSEUR_REFERENT->name,
                $faker->firstName(),
                $faker->lastName(),
                $faker->boolean(95)
            );
            $manager->persist($professeur);
            $professeurs[] = $professeur;

            // 3. Création de 10 alternants par formation
            for ($a = 0; $a < 10; ++$a) {
                $u_alternant = $this->creerUtilisateur(
                    Role::ALTERNANT->name, // Correction : Role Alternant ici
                    $faker->firstName(),
                    $faker->lastName(),
                    $faker->boolean(95)
                );
                $manager->persist($u_alternant);

                $alternant = new Alternant();
                $alternant->setActif($u_alternant->isActif())
                    ->setDateCreation(new \DateTime('now'))
                    ->setUtilisateur($u_alternant)
                    ->setFormation($formation);
                $manager->persist($alternant);
                $alternants[] = $alternant;

                // Suivi pédagogique (aligné sur les dates de la formation)
                $suiviPedagogique = new SuiviPedagogique();
                $suiviPedagogique
                    ->setAlternant($alternant)
                    ->setFormation($formation)
                    ->setProfesseur($professeur)
                    ->setEstPrincipal(true)
                    ->setActif($professeur->isActif() && $u_alternant->isActif())
                    ->setDateDebut($formation->getDateDebut())
                    ->setDateFin($formation->getDateFin());
                $manager->persist($suiviPedagogique);

                $this->creerFichesPourAlternant($alternant, $formation, $professeur, $manager, $faker);
            }
        }

        // 4. Création des tuteurs (1 par alternant)
        foreach ($alternants as $alternant) {
            $tuteur = $this->creerUtilisateur(
                Role::TUTEUR->name,
                $faker->firstName(),
                $faker->lastName(),
                $faker->boolean(95)
            );
            $manager->persist($tuteur);

            $tutorat = new Tutorat();
            $tutorat->setTuteur($tuteur)
                ->setAlternant($alternant)
                ->setActif($alternant->isActif() && $tuteur->isActif())
                ->setDateDebut($alternant->getFormation()->getDateDebut())
                ->setDateFin($alternant->getFormation()->getDateFin());
            $manager->persist($tutorat);
        }

        // 5. Création de l'administrateur
        $admin = $this->creerUtilisateur(
            Role::ADMINISTRATEUR->name,
            'admin',
            'admin',
            true
        );
        $manager->persist($admin);

        $manager->flush();
    }

    private function creerUtilisateur(string $role, string $nom, string $prenom, bool $actif): Utilisateur
    {
        $u = new Utilisateur();
        $u->setActif($actif)
            ->setEmail(strtolower($nom.'.'.$prenom).'@gmail.com')
            ->setNom($nom)
            ->setPrenom($prenom)
            ->setMotDePasse($this->passwordHasher->hashPassword($u, 'password123'))
            ->setRole($role)
            ->setDateCreation(new \DateTime('now'));

        return $u;
    }

    private function creerFichesPourAlternant(Alternant $alternant, Formation $formation, Utilisateur $commentaireAuteur, ObjectManager $manager, Faker\Generator $faker): void
    {
        $statuses = [
            StatusFiche::VALIDE,
            StatusFiche::CRITERES_NON_REMPLIS,
            StatusFiche::VALIDE,
            StatusFiche::SOUMISE,
            StatusFiche::BROUILLON,
        ];

        $periodeDebut = (clone $formation->getDateDebut())->setTime(9, 0, 0);

        for ($i = 0; $i < 5; ++$i) {
            $ficheDebut = (clone $periodeDebut)->modify("+$i week");
            if ($ficheDebut > $formation->getDateFin()) {
                break;
            }

            $ficheFin = (clone $ficheDebut)->modify('friday this week')->setTime(17, 0, 0);
            if ($ficheFin > $formation->getDateFin()) {
                $ficheFin = (clone $formation->getDateFin())->setTime(17, 0, 0);
            }

            $status = $statuses[$i];
            $validateur = 0 === $i % 2 ? 'entreprise' : 'formation';
            $fiche = new Fiche();
            $fiche->setAlternant($alternant)
                ->setDateDebut($ficheDebut)
                ->setDateFin($ficheFin)
                ->setDateCreation(new \DateTime('now'))
                ->setStatusFiche($status)
                ->setValidateur($validateur);

            if (StatusFiche::BROUILLON !== $status) {
                $fiche->setDateSoumission(clone $ficheFin);
            }
            if (StatusFiche::VALIDE === $status || StatusFiche::CRITERES_NON_REMPLIS === $status) {
                $fiche->setDateValidation(clone $ficheFin);
            }

            $manager->persist($fiche);

            for ($j = 0; $j < 5; ++$j) {
                $dateTache = (clone $ficheDebut)->modify("+$j day");
                if ($dateTache > $ficheFin) {
                    $dateTache = clone $ficheFin;
                }

                $tache = new Tache();
                $description = sprintf('Tâche %d pour la fiche %d de %s', $j + 1, $i + 1, $alternant->getUtilisateur()?->getPrenom() ?? 'alternant');
                $tache->setDescription($description)
                    ->setDateTache($dateTache)
                    ->setDateCreation(new \DateTime('now'))
                    ->setFiche($fiche);

                $tache->setCategorie($faker->randomElement(['autonomie', 'surveille', 'observation', 'ferie', 'absence']));

                $manager->persist($tache);
                $fiche->addTach($tache);
            }

            if (StatusFiche::CRITERES_NON_REMPLIS === $status) {
                $commentaire = new Commentaire();
                $commentaire->setCommentaire('Une tâche est en critères non remplis, merci de vérifier les éléments manquants.')
                    ->setDateCreation(new \DateTime('now'))
                    ->setAuteur($commentaireAuteur)
                    ->setFiche($fiche);

                $manager->persist($commentaire);
                $fiche->addCommentaire($commentaire);
            }
        }
    }

    private function genererFormationsAutomatique()
    {
        $config = [
            'Formation DWWM' => 9,
            'DUT' => 24,
            'BTS' => 36,
        ];

        $aujourdhui = new \DateTime('2026-04-01');

        $debutDWWM1 = (clone $aujourdhui)->modify('-4 months');
        $debutDWWM2 = (clone $aujourdhui)->modify('-2 months');
        $debutDUT1 = new \DateTime('first Monday of September 2024');
        $debutDUT2 = new \DateTime('first Monday of September 2025');
        $debutBTS = new \DateTime('first Monday of September 2024');

        $entrees = [
            ['nom' => 'Formation DWWM', 'session' => 'session 1(9 mois)', 'debut' => $debutDWWM1->format('Y-m-d')],
            ['nom' => 'Formation DWWM', 'session' => 'session 2(9 mois)', 'debut' => $debutDWWM2->format('Y-m-d')],
            ['nom' => 'DUT', 'session' => '2024/2026', 'debut' => $debutDUT1->format('Y-m-d')],
            ['nom' => 'DUT', 'session' => '2025/2027', 'debut' => $debutDUT2->format('Y-m-d')],
            ['nom' => 'BTS', 'session' => '2024/2027', 'debut' => $debutBTS->format('Y-m-d')],
        ];

        $formations = [];
        foreach ($entrees as $item) {
            $nom = $item['nom'];
            $dateDebut = new \DateTime($item['debut'].' 09:00:00');
            $dateFin = clone $dateDebut;

            if (isset($config[$nom])) {
                $dureeMois = $config[$nom];
                $dateFin->modify("+$dureeMois months");
                if ('DUT' === $nom || 'BTS' === $nom) {
                    $dateFin->modify('last friday of June '.$dateFin->format('Y'));
                } else {
                    $dateFin->modify('-3 days');
                }
                $dateFin->setTime(17, 0, 0);
            }

            $formations[] = [
                'nom' => $nom,
                'descrription' => "Cursus $nom - Session ".$item['session'],
                'session' => $item['session'],
                'datedebut' => $dateDebut->format('Y-m-d H:i:s'),
                'datefin' => $dateFin->format('Y-m-d H:i:s'),
            ];
        }

        return $formations;
    }
}
