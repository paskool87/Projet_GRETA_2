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
use DateInterval;
use DateTime;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create("fr_FR");
        date_default_timezone_set("UTC");
        // $utilisateur = new utilisateur();
        // $manager->persist($utilisateur);
        $utilisateurs = [];
        $alternants = [];
        $proffeseurs = [];
        $tuteurs = [];
        $formations = [];

        for ($i = 0; $i < 5; $i++) {
            $formation = new Formation();
            $formation->setNomFormation($faker->word() . "" . $i)
                ->setDescription($faker->sentence())
                ->setSession($faker->date("Y") . "-" . $faker->date("Y"));
            $manager->persist($formation);
            $formations[] = $formation;
        };

        //Creation des professeurs
        for ($i = 0; $i < 5; $i++) {
            $professeur = new Utilisateur();
            $professeur->setActif($faker->boolean(90))
                ->setEmail($faker->email())
                ->setNom($faker->firstName())
                ->setPrenom($faker->lastName())
                ->setMotDePasse($faker->password())
                ->setRole(Role::PROFESSEUR_REFERENT->label());
            $manager->persist($professeur);
            $professeurs[] = $professeur;
        }

        for ($i = 0; $i < 50; $i++) {
            // Creation desutilisateur pour les alternants
            $u_alternant = new Utilisateur();
            $u_alternant->setActif($faker->boolean(90))
                ->setEmail($faker->email())
                ->setNom($faker->firstName())
                ->setPrenom($faker->lastName())
                ->setMotDePasse($faker->password())
                ->setRole(Role::ALTERNAT->label());
            $manager->persist($u_alternant);

            // Creation des alternant 
            $alternant = new Alternant();
            $alternant->setActif($faker->boolean(95))
                ->setDateCreation(new DateTime("now"))
                ->setUtilisateurId($u_alternant)
                ->setFormationId($formations[array_rand($formations)]);
            $manager->persist($alternant);
            $alternants[] = $alternant;

            //Creation des suivi pedagogique
            $suiviPedagogique = new SuiviPedagogique();
            $id = $i % 5 > 5 ? $i % 5 : 0;
            $suiviPedagogique
                ->setAlternantId($alternant)
                ->setFormationId($alternant->getFormationId())
                ->setProfesseur($professeurs[$id])
                ->setEstPrincipal(true)
                ->setActif(true)
                ->setDateDebut(new DateTime())
                ->setDateFin($faker->dateTimeBetween("now", "+30 years"));
            $manager->persist($suiviPedagogique);
        };

        // Creation des tuteurs
        for ($i = 0; $i < 50; $i++) {
            $tuteur = new Utilisateur();
            $tuteur->setActif($faker->boolean(90))
                ->setEmail($faker->email())
                ->setNom($faker->firstName())
                ->setPrenom($faker->lastName())
                ->setMotDePasse($faker->password())
                ->setRole(Role::TUTEUR->label());
            $manager->persist($tuteur);

            $tuteurs[] = $tuteur;

            // Creation des tutorats
            $tutorat = new Tutorat();
            $tutorat->setTuteurId($tuteur)
                ->setAlternantId($alternants[$i])
                ->setActif(true)
                ->setDateDebut(new DateTime())
                ->setDateFin($faker->dateTimeBetween("now", "+30 years"));
            $manager->persist($tutorat);
        };


        //Creation de l'administrateur
        $admin = new Utilisateur();
        $admin->setActif(true)
            ->setEmail("admin@gmail.com")
            ->setNom("admin")
            ->setPrenom("admin")
            ->setMotDePasse("admin")
            ->setRole(Role::ADMINISTRATEUR->label());
        $manager->persist($admin);
        $admin = new Utilisateur();


        //Creation des fiches, taches et commentaire
        //2 fiches par alternant, 5 taches par fiches

        $utilisateurs = $professeurs + $tuteurs;
        for ($i = 0; $i < 2; $i++) {
            foreach ($alternants as $alternant) {
                $fiche = new Fiche();
                $rand_Role = $faker->randomElement(StatusFiche::cases());
                $debut = $faker->dateTime();
                $fin = date_add($debut, new DateInterval("P7D"));
                $fiche->setAlternantId($alternant)
                    ->setDateDebut($debut)
                    ->setDateFin($fin)
                    ->setStatusFiche($rand_Role)
                    ->setDateCreation($debut)
                ;
                if ($rand_Role == StatusFiche::SOUMISE || $rand_Role == StatusFiche::VALIDE) {
                    $soumis = $faker->dateTimeBetween($debut, $fin);
                    $fiche->setDateSoumission($soumis);
                    if ($rand_Role == StatusFiche::VALIDE) {
                        $fiche->setDateValidation($faker->dateTimeBetween($soumis, $fin));
                    }
                }
                $manager->persist($fiche);

                if ($faker->boolean(50)) {
                    $commentaire = new Commentaire();
                    $commentaire->setFicheId($fiche)
                        ->setCommentaire($faker->sentence($faker->numberBetween(1, 20)))
                        ->setAuteurId($faker->randomElement($utilisateurs))->setDateCreation($faker->dateTimeBetween($debut, $fin))
                    ;   //manque date creation
                    $manager->persist($commentaire);
                }
                for ($i = 0; $i < $faker->numberBetween(1, 5); $i++) {
                    $tache = new Tache();
                    $etat = $faker->numberBetween(1, 5);
                    $message = $faker->sentence($faker->numberBetween(1, 20));
                    if ($etat >= 3) {
                        match ($etat) {
                            1 => $tache->setAutonomie(true),
                            2 => $tache->setObservation(true),
                            3 => $tache->setSurveille(true),
                            default => $tache->setAutonomie(true),
                        };
                    } else {
                        $message = $etat == 4 ? "Absence" : "Jours feries";
                        match ($etat) {
                            4 => $tache->setAbsence(true),
                            5 => $tache->setFerie(true),
                            default => $tache->setAbsence(true),
                        };
                    }
                    $tache->setTacheAcomplie($message);
                    $tache->setFicheId($fiche)
                        ->setDateTache($faker->dateTimeBetween($debut, $fin))
                        ->setDateCreation($faker->dateTimeBetween($debut, $fin));
                    $manager->persist($tache);
                }
            }
        }
        $manager->flush();
    }
}