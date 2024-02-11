<?php

namespace App\Entity;

use App\Repository\TypeNameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeNameRepository::class)]
class TypeName
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?float $score = null;

    #[ORM\ManyToOne]
    private ?Actiontype $actiontype = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Action::class)]
    private Collection $actions;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $materiel = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $type = null;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getActiontype(): ?Actiontype
    {
        return $this->actiontype;
    }

    public function setActiontype(?Actiontype $actiontype): static
    {
        $this->actiontype = $actiontype;

        return $this;
    }

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setType($this);
        }

        return $this;
    }

    public function removeAction(Action $action): static
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getType() === $this) {
                $action->setType(null);
            }
        }

        return $this;
    }

    public function getMateriel(): ?string
    {
        return $this->materiel;
    }

    public function setMateriel(?string $materiel): static
    {
        $this->materiel = $materiel;

        return $this;
    }
    public function __toString()
    {
        return $this->getNom(); 
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
