<?php

namespace App\Entity;

use App\Enum\Role;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 45)]
    private ?string $nom = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 45)]
    private ?string $prenom = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 64)]
    private ?string $email = null;

    #[ORM\Column(length: 80)]
    private ?string $mot_de_passe = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 240)]
    private ?string $role;

    #[Groups(['user:read'])]
    #[ORM\Column]
    private ?bool $actif = null;



    /**
     * @var Collection<int, Tutorat>
     */
    #[ORM\OneToMany(targetEntity: Tutorat::class, mappedBy: 'tuteur')]
    private Collection $tutorats;

    /**
     * @var Collection<int, SuiviPedagogique>
     */
    #[ORM\OneToMany(targetEntity: SuiviPedagogique::class, mappedBy: 'professeur')]
    private Collection $suiviPedagogiques;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTime $date_creation = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'auteur')]
    private Collection $commentaires;

    public function __construct()
    {
        $this->tutorats = new ArrayCollection();
        $this->suiviPedagogiques = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return match (Role::tryFrom($this->role)) {
            Role::ADMINISTRATEUR       => ['ROLE_ADMIN', 'ROLE_USER'],
            Role::PROFESSEUR_REFERENT  => ['ROLE_PROFESSEUR', 'ROLE_USER'],
            Role::ALTERNANT             => ['ROLE_ALTERNANT', 'ROLE_USER'],
            Role::TUTEUR               => ['ROLE_TUTEUR', 'ROLE_USER'],
            default                             => ['ROLE_USER'],
        };
    }




    public function getPassword(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setPassword(string $password): static
    {
        $this->mot_de_passe = $password;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Tutorat>
     */
    public function getTutorats(): Collection
    {
        return $this->tutorats;
    }

    public function addTutorat(Tutorat $tutorat): static
    {
        if (!$this->tutorats->contains($tutorat)) {
            $this->tutorats->add($tutorat);
            $tutorat->setTuteur($this);
        }

        return $this;
    }

    public function removeTutorat(Tutorat $tutorat): static
    {
        if ($this->tutorats->removeElement($tutorat)) {
            // set the owning side to null (unless already changed)
            if ($tutorat->getTuteur() === $this) {
                $tutorat->setTuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SuiviPedagogique>
     */
    public function getSuiviPedagogiques(): Collection
    {
        return $this->suiviPedagogiques;
    }

    public function addSuiviPedagogique(SuiviPedagogique $suiviPedagogique): static
    {
        if (!$this->suiviPedagogiques->contains($suiviPedagogique)) {
            $this->suiviPedagogiques->add($suiviPedagogique);
            $suiviPedagogique->setProfesseur($this);
        }

        return $this;
    }

    public function removeSuiviPedagogique(SuiviPedagogique $suiviPedagogique): static
    {
        if ($this->suiviPedagogiques->removeElement($suiviPedagogique)) {
            // set the owning side to null (unless already changed)
            if ($suiviPedagogique->getProfesseur() === $this) {
                $suiviPedagogique->setProfesseur(null);
            }
        }

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setAuteur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getAuteur() === $this) {
                $commentaire->setAuteur(null);
            }
        }

        return $this;
    }
}
