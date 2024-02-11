<?php

namespace App\Entity;

use App\Repository\ActiontypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActiontypeRepository::class)]
class Actiontype
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?int $niveau_danger = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $type_score = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;




    public function getId(): ?int
    {
        return $this->id;
    }



    public function getNiveauDanger(): ?int
    {
        return $this->niveau_danger;
    }

    public function setNiveauDanger(int $niveau_danger): static
    {
        $this->niveau_danger = $niveau_danger;

        return $this;
    }

    public function getTypeScore(): ?string
    {
        return $this->type_score;
    }

    public function setTypeScore(?string $type_score): static
    {
        $this->type_score = $type_score;

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
    public function __toString()
    {
        return $this->getNom(); 
    }


}
