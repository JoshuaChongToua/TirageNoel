<?php

namespace App\Entity;

use App\Repository\RestrictionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestrictionRepository::class)]
class Restriction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'restrictions')]
    private ?User $joueur = null;

    #[ORM\ManyToOne(inversedBy: 'restrictions')]
    private ?User $interdit = null;

    #[ORM\ManyToOne(inversedBy: 'restrictions')]
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

    public function getInterdit(): ?User
    {
        return $this->interdit;
    }

    public function setInterdit(?User $interdit): static
    {
        $this->interdit = $interdit;

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
