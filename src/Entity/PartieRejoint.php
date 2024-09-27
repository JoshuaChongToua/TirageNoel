<?php

namespace App\Entity;

use App\Repository\PartieRejointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartieRejointRepository::class)]
class PartieRejoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\ManyToOne(inversedBy: 'partieRejoints')]
    private ?Partie $partie = null;

    #[ORM\ManyToOne(inversedBy: 'partieRejoints')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private ?array $souhaits = [];
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartie(): ?partie
    {
        return $this->partie;
    }

    public function setPartie(?partie $partie): static
    {
        $this->partie = $partie;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getSouhaits(): ?array
    {
        return $this->souhaits;
    }

    public function setSouhaits(?array $souhaits): static
    {
        $this->souhaits = $souhaits;

        return $this;
    }


}
