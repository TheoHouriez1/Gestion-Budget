<?php
namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?User $iduser = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Transaction::class)]
    private $transactions; // Ajout de la relation inversée

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

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): static
    {
        $this->iduser = $iduser;

        return $this;
    }

    // Getter et setter pour la relation inversée
    public function getTransactions()
    {
        return $this->transactions;
    }

    public function setTransactions($transactions): static
    {
        $this->transactions = $transactions;
        return $this;
    }
}
