<?php
namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;  // Remplacement de la colonne 'category' par une relation ManyToOne

    #[ORM\Column(length: 400)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?CompteBudget $Compte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getCategory(): ?Category  // Méthode getter mise à jour pour la relation ManyToOne
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static  // Méthode setter mise à jour pour la relation ManyToOne
    {
        $this->category = $category;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getCompte(): ?CompteBudget
    {
        return $this->Compte;
    }

    public function setCompte(?CompteBudget $Compte): static
    {
        $this->Compte = $Compte;
        return $this;
    }
}
