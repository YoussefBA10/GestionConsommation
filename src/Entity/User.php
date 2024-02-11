<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $user_score = null;

    #[ORM\Column(nullable: true)]
    private ?int $bonus = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getUserScore(): ?int
    {
        return $this->user_score;
    }

    public function setUserScore(int $user_score): static
    {
        $this->user_score = $user_score;

        return $this;
    }
    public function __toString()
    {
        return $this->getNom(); 
    }

    public function getBonus(): ?int
    {
        return $this->bonus;
    }

    public function setBonus(?int $bonus): static
    {
        $this->bonus = $bonus;

        return $this;
    }
}
