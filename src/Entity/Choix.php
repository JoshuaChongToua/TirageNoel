<?php

namespace App\Entity;

use App\Repository\ChoixRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChoixRepository::class)]
class Choix
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'choixes')]
    private ?User $joueur = null;

    #[ORM\ManyToOne(inversedBy: 'choixes')]
    private ?User $personneChoisie = null;

    #[ORM\ManyToOne(inversedBy: 'choixes')]
    private ?Partie $partie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJoueur(): ?User
    {
        return $this->joueur;
    }

    public function setJoueur(?User $joueur): static
    {
        $this->joueur = $joueur;

        return $this;
    }

    public function getPersonneChoisie(): ?User
    {
        return $this->personneChoisie;
    }

    public function setPersonneChoisie(?User $personneChoisie): static
    {
        $this->personneChoisie = $personneChoisie;

        return $this;
    }

    public function getPartie(): ?Partie
    {
        return $this->partie;
    }

    public function setPartie(?Partie $partie): static
    {
        $this->partie = $partie;

        return $this;
    }
}
