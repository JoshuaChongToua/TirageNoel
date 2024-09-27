<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null ;

    /**
     * @var Collection<int, PartieCreate>
     */
    #[ORM\OneToMany(targetEntity: PartieCreate::class, mappedBy: 'user')]
    private Collection $partieCreates;

    /**
     * @var Collection<int, PartieRejoint>
     */
    #[ORM\OneToMany(targetEntity: PartieRejoint::class, mappedBy: 'user')]
    private Collection $partieRejoints;

    /**
     * @var Collection<int, TirageResultat>
     */
    #[ORM\OneToMany(targetEntity: TirageResultat::class, mappedBy: 'joueur')]
    private Collection $tirageResultats;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: TirageResultat::class)]
    private Collection $tiragesEnTantQueDestinataire;

    #[ORM\OneToMany(mappedBy: 'createur', targetEntity: Partie::class)]
    private Collection $parties;

    #[ORM\ManyToOne(inversedBy: 'joueur')]
    private ?Choix $choix = null;

    /**
     * @var Collection<int, Choix>
     */
    #[ORM\OneToMany(targetEntity: Choix::class, mappedBy: 'joueur')]
    private Collection $choixes;
    

    public function __construct()
    {
        $this->partieCreates = new ArrayCollection();
        $this->partieRejoints = new ArrayCollection();
        $this->tirageResultats = new ArrayCollection();
        $this->parties = new ArrayCollection();
        $this->tiragesEnTantQueDestinataire = new ArrayCollection();
        $this->choixes = new ArrayCollection();

    }
   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        return ['ROLE_USER']; // Valeur par défaut
    }
    
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getRole(): ?role
    {
        return $this->role;
    }

    public function setRole(?role $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, PartieCreate>
     */
    public function getPartieCreates(): Collection
    {
        return $this->partieCreates;
    }

    public function addPartieCreate(PartieCreate $partieCreate): static
    {
        if (!$this->partieCreates->contains($partieCreate)) {
            $this->partieCreates->add($partieCreate);
            $partieCreate->setUser($this);
        }

        return $this;
    }

    public function removePartieCreate(PartieCreate $partieCreate): static
    {
        if ($this->partieCreates->removeElement($partieCreate)) {
            // set the owning side to null (unless already changed)
            if ($partieCreate->getUser() === $this) {
                $partieCreate->setUser(null);
            }
        }

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
            $partieRejoint->setUser($this);
        }

        return $this;
    }

    public function removePartieRejoint(PartieRejoint $partieRejoint): static
    {
        if ($this->partieRejoints->removeElement($partieRejoint)) {
            // set the owning side to null (unless already changed)
            if ($partieRejoint->getUser() === $this) {
                $partieRejoint->setUser(null);
            }
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
            $tirageResultat->setJoueur($this);
        }

        return $this;
    }

    public function removeTirageResultat(TirageResultat $tirageResultat): static
    {
        if ($this->tirageResultats->removeElement($tirageResultat)) {
            // set the owning side to null (unless already changed)
            if ($tirageResultat->getJoueur() === $this) {
                $tirageResultat->setJoueur(null);
            }
        }

        return $this;
    }


    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addPartie(Partie $partie): self
    {
        if (!$this->parties->contains($partie)) {
            $this->parties[] = $partie;
            $partie->setCreateur($this);
        }

        return $this;
    }

    public function removePartie(Partie $partie): self
    {
        if ($this->parties->removeElement($partie)) {
            // Unset the owning side
            if ($partie->getCreateur() === $this) {
                $partie->setCreateur(null);
            }
        }

        return $this;
    }

    public function getTiragesEnTantQueDestinataire(): Collection
    {
        return $this->tiragesEnTantQueDestinataire;
    }

    public function addTirageEnTantQueDestinataire(TirageResultat $tirageResultat): self
    {
        if (!$this->tiragesEnTantQueDestinataire->contains($tirageResultat)) {
            $this->tiragesEnTantQueDestinataire[] = $tirageResultat;
            $tirageResultat->setDestinataire($this);
        }

        return $this;
    }

    public function removeTirageEnTantQueDestinataire(TirageResultat $tirageResultat): self
    {
        if ($this->tiragesEnTantQueDestinataire->removeElement($tirageResultat)) {
            // Unset the owning side
            if ($tirageResultat->getDestinataire() === $this) {
                $tirageResultat->setDestinataire(null);
            }
        }

        return $this;
    }

    public function getChoix(): ?Choix
    {
        return $this->choix;
    }

    public function setChoix(?Choix $choix): static
    {
        $this->choix = $choix;

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
            $choix->setJoueur($this);
        }

        return $this;
    }

    public function removeChoix(Choix $choix): static
    {
        if ($this->choixes->removeElement($choix)) {
            // set the owning side to null (unless already changed)
            if ($choix->getJoueur() === $this) {
                $choix->setJoueur(null);
            }
        }

        return $this;
    }

    

}
