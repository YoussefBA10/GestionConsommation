<?php

namespace App\Entity;
use App\Entity\User;
use App\Repository\ActionRepository;
use App\Repository\ActiontypeRepository;
use App\Repository\TypeNameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 55, nullable: true)]
    private ?string $quantite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 55, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $action_score = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?TypeName $type = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $quantite_time = null;

    #[ORM\ManyToOne]
    private ?Actiontype $categorie = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $niveau_danger = null;




 
   
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getQuantite(): ?string
    {
        return $this->quantite;
    }

    public function setQuantite(string $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getActionScore(): ?int
    {
        return $this->action_score;
    }

    public function setActionScore(int $action_score): static
    {
        $this->action_score = $action_score;
        return $this;
    }
    public function getTypeMateriel(int $id,ActiontypeRepository $rep): static
    {
        $actiontype=$rep->findById();
        return $actiontype;
    }

    public function getType(): ?TypeName
    {
        return $this->type;
    }

    public function setType(?TypeName $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantiteTime(): ?\DateTimeInterface
    {
        return $this->quantite_time;
    }

    public function setQuantiteTime(?\DateTimeInterface $quantite_time): static
    {
        $this->quantite_time = $quantite_time;

        return $this;
    }

    public function getCategorie(): ?actiontype
    {
        return $this->categorie;
    }

    public function setCategorie(?actiontype $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getNiveauDanger(): ?string
    {
        return $this->niveau_danger;
    }

    public function setNiveauDanger(?string $niveau_danger): static
    {
        $this->niveau_danger = $niveau_danger;

        return $this;
    }
    public static function calculePourcentage(array $actions, User $user,string $materielle): float
{
     // Assuming 'value' and 'total' are properties of the action entity
     $totalValue = 0;
     $totalTotal = 0;
     
     foreach ($actions as $action) {
         if ($action->getUser() === $user && $action->getType()->getMateriel() === $materielle) {
             $totalValue += $action->getActionScore();
         }
         else if ($action === $user){
             $totalTotal += $action->getActionScore();
         }
     }

     if ($totalTotal != 0) {
         return round(($totalValue / $totalTotal) * 100, 2);
     } else {
         return 0;
     }
    return 0;
}


  
    
}
