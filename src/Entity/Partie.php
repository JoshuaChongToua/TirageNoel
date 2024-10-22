<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartieRepository::class)]
class Partie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'parties')]
    private ?User $createur = null;


    /**
     * @var Collection<int, PartieRejoint>
     */
    #[ORM\OneToMany(targetEntity: PartieRejoint::class, mappedBy: 'partie', cascade:['remove'], orphanRemoval: true)]
    private Collection $partieRejoints;

    #[ORM\OneToOne(mappedBy: 'partie', cascade: ['persist', 'remove'], targetEntity: PartieCreate::class, orphanRemoval: true)]
    private ?PartieCreate $partieCreate = null;

    /**
     * @var Collection<int, TirageResultat>
     */
    #[ORM\OneToMany(targetEntity: TirageResultat::class, mappedBy: 'partie', orphanRemoval: true)]
    private Collection $tirageResultats;

    /**
     * @var Collection<int, Choix>
     */
    #[ORM\OneToMany(targetEntity: Choix::class, mappedBy: 'partie')]
    private Collection $choixes;

    /**
     * @var Collection<int, Restriction>
     */
    #[ORM\OneToMany(targetEntity: Restriction::class, mappedBy: 'partie')]
    private Collection $restrictions;


    public function __construct()
    {
        $this->partieRejoints = new ArrayCollection();
        $this->tirageResultats = new ArrayCollection();
        $this->choixes = new ArrayCollection();
        $this->restrictions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }


    public function getCreateur(): ?user
    {
        return $this->createur;
    }

    public function setCreateur(?user $createur): static
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * @return Collection<int, PartieRejoint>
     */
    public function getPartieRejoints(): Collection
    {
        return $this->partieRejoints;
    }

    public function addPartieRejoint(PartieRejoint $partieRejoint): static
    {
        if (!$this->partieRejoints->contains($partieRejoint)) {
            $this->partieRejoints->add($partieRejoint);
            $partieRejoint->setPartie($this);
        }

        return $this;
    }

    public function removePartieRejoint(PartieRejoint $partieRejoint): static
    {
        if ($this->partieRejoints->removeElement($partieRejoint)) {
            // set the owning side to null (unless already changed)
            if ($partieRejoint->getPartie() === $this) {
                $partieRejoint->setPartie(null);
            }
        }

        return $this;
    }

    public function getPartieCreate(): ?PartieCreate
    {
        return $this->partieCreate;
    }

    public function setPartieCreate(?PartieCreate $partieCreate): self
    {
        $this->partieCreate = $partieCreate;

        if ($partieCreate !== null) {
            $partieCreate->setPartie($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TirageResultat>
     */
    public function getTirageResultats(): Collection
    {
        return $this->tirageResultats;
    }

    public function addTirageResultat(TirageResultat $tirageResultat): static
    {
        if (!$this->tirageResultats->contains($tirageResultat)) {
            $this->tirageResultats->add($tirageResultat);
            $tirageResultat->setPartie($this);
        }

        return $this;
    }

    public function removeTirageResultat(TirageResultat $tirageResultat): static
    {
        if ($this->tirageResultats->removeElement($tirageResultat)) {
            // set the owning side to null (unless already changed)
            if ($tirageResultat->getPartie() === $this) {
                $tirageResultat->setPartie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Choix>
     */
    public function getChoixes(): Collection
    {
        return $this->choixes;
    }

    public function addChoix(Choix $choix): static
    {
        if (!$this->choixes->contains($choix)) {
            $this->choixes->add($choix);
            $choix->setPartie($this);
        }

        return $this;
    }

    public function removeChoix(Choix $choix): static
    {
        if ($this->choixes->removeElement($choix)) {
            // set the owning side to null (unless already changed)
            if ($choix->getPartie() === $this) {
                $choix->setPartie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Restriction>
     */
    public function getRestrictions(): Collection
    {
        return $this->restrictions;
    }

    public function addRestriction(Restriction $restriction): static
    {
        if (!$this->restrictions->contains($restriction)) {
            $this->restrictions->add($restriction);
            $restriction->setPartie($this);
        }

        return $this;
    }

    public function removeRestriction(Restriction $restriction): static
    {
        if ($this->restrictions->removeElement($restriction)) {
            // set the owning side to null (unless already changed)
            if ($restriction->getPartie() === $this) {
                $restriction->setPartie(null);
            }
        }

        return $this;
    }

   
}
